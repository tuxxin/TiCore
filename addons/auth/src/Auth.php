<?php
namespace Tuxxin\TiCore\Addons\Auth;

/**
 * Self-contained SQLite-backed auth service. Table auto-creates on first use
 * (same convenience pattern as qr-track). DB lives outside the web root.
 */
final class Auth
{
    private \PDO $db;

    public function __construct(?string $dbPath = null)
    {
        $dbPath ??= (defined('AUTH_DB_PATH') ? AUTH_DB_PATH : CORE_PATH . '/database/auth.sqlite');
        $dir = dirname($dbPath);
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
        $this->db = new \PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $this->ensureSchema();
    }

    private function ensureSchema(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS ti_auth_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            name TEXT,
            password_hash TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'user',
            email_verified INTEGER NOT NULL DEFAULT 0,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function findByEmail(string $email): ?array
    {
        $s = $this->db->prepare("SELECT * FROM ti_auth_users WHERE email = ?");
        $s->execute([strtolower(trim($email))]);
        return $s->fetch() ?: null;
    }

    public function create(string $email, string $password, ?string $name = null, string $role = 'user'): array
    {
        $s = $this->db->prepare("INSERT INTO ti_auth_users (email, name, password_hash, role) VALUES (?,?,?,?)");
        $s->execute([strtolower(trim($email)), $name, password_hash($password, PASSWORD_DEFAULT), $role]);
        return $this->findByEmail($email);
    }

    public function verifyPassword(array $user, string $password): bool
    {
        return password_verify($password, $user['password_hash'] ?? '');
    }

    public function login(array $user): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_regenerate_id(true); // prevent fixation
        $_SESSION['user_id']    = (int) $user['id'];
        $_SESSION['user_role']  = $user['role'] ?? 'user';
        $_SESSION['user_email'] = $user['email'] ?? '';
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['user_id'], $_SESSION['user_role'], $_SESSION['user_email']);
        session_regenerate_id(true);
    }

    public function current(): ?array
    {
        if (empty($_SESSION['user_id'])) return null;
        $s = $this->db->prepare("SELECT id,email,name,role,email_verified,created_at FROM ti_auth_users WHERE id = ?");
        $s->execute([$_SESSION['user_id']]);
        return $s->fetch() ?: null;
    }
}
