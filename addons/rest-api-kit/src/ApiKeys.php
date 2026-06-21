<?php
namespace Tuxxin\TiCore\Addons\RestApiKit;

/**
 * Self-contained SQLite-backed API-key service (same convenience pattern as the
 * auth addon: tables auto-create on first use, DB outside the web root).
 *
 * Keys look like  tk_<48 hex>  — only a SHA-256 hash and a short display prefix
 * are stored; the full key is shown to the operator exactly once at creation.
 * Per-IP token-bucket rate limiting mirrors qr-track's windowed limiter.
 */
final class ApiKeys
{
    private \PDO $db;

    public function __construct(?string $dbPath = null)
    {
        $dbPath ??= (defined('RESTAPIKIT_DB_PATH') ? RESTAPIKIT_DB_PATH : CORE_PATH . '/database/rest-api-kit.sqlite');
        $dir = dirname($dbPath);
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
        $this->db = new \PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $this->ensureSchema();
    }

    private function ensureSchema(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS ti_restapikit_keys (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            key_prefix TEXT NOT NULL,
            key_hash TEXT NOT NULL,
            active INTEGER NOT NULL DEFAULT 1,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            last_used_at TEXT
        )");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_restapikit_prefix ON ti_restapikit_keys (key_prefix)");
        $this->db->exec("CREATE TABLE IF NOT EXISTS ti_restapikit_ratelimit (
            ip TEXT PRIMARY KEY,
            window_start INTEGER NOT NULL,
            request_count INTEGER NOT NULL DEFAULT 0
        )");
    }

    // ── Keys (admin) ───────────────────────────────────────────────────────────

    /** @return array<int,array> all keys, newest first (never exposes the secret). */
    public function all(): array
    {
        return $this->db->query(
            "SELECT id, name, key_prefix, active, created_at, last_used_at
             FROM ti_restapikit_keys ORDER BY id DESC"
        )->fetchAll();
    }

    /**
     * Mint a new key. Returns ['record'=>…, 'plaintext'=>'tk_...'] — show the
     * plaintext to the operator ONCE; only the hash + prefix are persisted.
     */
    public function create(string $name): array
    {
        $name = trim($name);
        if ($name === '') $name = 'Untitled key';

        $plain  = 'tk_' . bin2hex(random_bytes(24));      // tk_ + 48 hex chars
        $prefixLen = (int) (config('rest-api-kit')['prefix_len'] ?? 10);
        $prefix = substr($plain, 0, max(6, $prefixLen));  // e.g. tk_1a2b3c (display only)
        $hash   = hash('sha256', $plain);

        $s = $this->db->prepare(
            "INSERT INTO ti_restapikit_keys (name, key_prefix, key_hash, active) VALUES (?,?,?,1)"
        );
        $s->execute([$name, $prefix, $hash]);
        $id = (int) $this->db->lastInsertId();

        return ['record' => $this->find($id), 'plaintext' => $plain];
    }

    public function find(int $id): ?array
    {
        $s = $this->db->prepare(
            "SELECT id, name, key_prefix, active, created_at, last_used_at
             FROM ti_restapikit_keys WHERE id = ?"
        );
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    /** Revoke (deactivate) a key. */
    public function revoke(int $id): void
    {
        $s = $this->db->prepare("UPDATE ti_restapikit_keys SET active = 0 WHERE id = ?");
        $s->execute([$id]);
    }

    // ── Verification (middleware) ──────────────────────────────────────────────

    /**
     * Verify a presented full key. Looks up by prefix, then constant-time
     * compares the SHA-256 hash. Returns the active key record or null.
     */
    public function verify(string $presented): ?array
    {
        $presented = trim($presented);
        if ($presented === '') return null;

        $prefixLen = (int) (config('rest-api-kit')['prefix_len'] ?? 10);
        $prefix = substr($presented, 0, max(6, $prefixLen));
        $hash   = hash('sha256', $presented);

        $s = $this->db->prepare(
            "SELECT id, name, key_prefix, key_hash, active FROM ti_restapikit_keys
             WHERE key_prefix = ? AND active = 1"
        );
        $s->execute([$prefix]);

        foreach ($s->fetchAll() as $row) {
            if (hash_equals((string) $row['key_hash'], $hash)) {
                unset($row['key_hash']);
                return $row;
            }
        }
        return null;
    }

    public function touch(int $id): void
    {
        $s = $this->db->prepare("UPDATE ti_restapikit_keys SET last_used_at = CURRENT_TIMESTAMP WHERE id = ?");
        $s->execute([$id]);
    }

    // ── Rate limiting (per-IP token bucket / fixed window) ──────────────────────

    /**
     * Record one hit for $ip within a $window-second window allowing $limit hits.
     * Mirrors qr-track's INSERT-OR-REPLACE windowed counter.
     *
     * @return array{allowed:bool, limit:int, remaining:int, retry_after:int, reset_at:int}
     */
    public function hitRateLimit(string $ip, int $limit, int $window): array
    {
        $now = time();
        $windowStart = $now - $window;

        $s = $this->db->prepare("SELECT window_start, request_count FROM ti_restapikit_ratelimit WHERE ip = ?");
        $s->execute([$ip]);
        $row = $s->fetch();

        if (!$row || (int) $row['window_start'] < $windowStart) {
            // New window — reset to a single hit.
            $this->db->prepare(
                "INSERT OR REPLACE INTO ti_restapikit_ratelimit (ip, window_start, request_count) VALUES (?,?,1)"
            )->execute([$ip, $now]);
            return [
                'allowed'     => true,
                'limit'       => $limit,
                'remaining'   => max(0, $limit - 1),
                'retry_after' => 0,
                'reset_at'    => $now + $window,
            ];
        }

        $count   = (int) $row['request_count'];
        $resetAt = (int) $row['window_start'] + $window;

        if ($count >= $limit) {
            return [
                'allowed'     => false,
                'limit'       => $limit,
                'remaining'   => 0,
                'retry_after' => max(1, $resetAt - $now),
                'reset_at'    => $resetAt,
            ];
        }

        $this->db->prepare("UPDATE ti_restapikit_ratelimit SET request_count = request_count + 1 WHERE ip = ?")
                 ->execute([$ip]);
        return [
            'allowed'     => true,
            'limit'       => $limit,
            'remaining'   => max(0, $limit - ($count + 1)),
            'retry_after' => 0,
            'reset_at'    => $resetAt,
        ];
    }
}
