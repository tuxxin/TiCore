<?php
namespace Tuxxin\TiCore\Addons\BlogCms;

/**
 * Self-contained SQLite-backed blog service. Tables auto-create on first use
 * (same convenience pattern as the auth addon). DB lives outside the web root.
 */
final class Blog
{
    private \PDO $db;

    public function __construct(?string $dbPath = null)
    {
        $dbPath ??= (defined('BLOGCMS_DB_PATH') ? BLOGCMS_DB_PATH : CORE_PATH . '/database/blog-cms.sqlite');
        $dir = dirname($dbPath);
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
        $this->db = new \PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $this->ensureSchema();
    }

    private function ensureSchema(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS ti_blogcms_categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slug TEXT UNIQUE NOT NULL,
            name TEXT NOT NULL
        )");
        $this->db->exec("CREATE TABLE IF NOT EXISTS ti_blogcms_posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slug TEXT UNIQUE NOT NULL,
            title TEXT NOT NULL,
            body TEXT,
            excerpt TEXT,
            category_id INTEGER,
            status TEXT NOT NULL DEFAULT 'draft',
            published_at TEXT,
            updated_at TEXT DEFAULT CURRENT_TIMESTAMP
        )");
    }

    // ── Categories ───────────────────────────────────────────────────────────

    /** @return array<int,array> all categories ordered by name. */
    public function categories(): array
    {
        return $this->db->query("SELECT id, slug, name FROM ti_blogcms_categories ORDER BY name ASC")->fetchAll();
    }

    public function findCategory(string $slug): ?array
    {
        $s = $this->db->prepare("SELECT id, slug, name FROM ti_blogcms_categories WHERE slug = ?");
        $s->execute([$slug]);
        return $s->fetch() ?: null;
    }

    /** Find-or-create a category by name; returns its id (or null if name empty). */
    public function ensureCategory(string $name): ?int
    {
        $name = trim($name);
        if ($name === '') return null;
        $slug = $this->slugify($name);
        $existing = $this->findCategory($slug);
        if ($existing) return (int) $existing['id'];
        $s = $this->db->prepare("INSERT INTO ti_blogcms_categories (slug, name) VALUES (?, ?)");
        $s->execute([$slug, $name]);
        return (int) $this->db->lastInsertId();
    }

    // ── Posts (public) ─────────────────────────────────────────────────────────

    /** Published posts, newest first. */
    public function publishedList(): array
    {
        $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug
                FROM ti_blogcms_posts p
                LEFT JOIN ti_blogcms_categories c ON c.id = p.category_id
                WHERE p.status = 'published'
                ORDER BY COALESCE(p.published_at, p.updated_at) DESC, p.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    /** Published posts in a category (by category slug), newest first. */
    public function publishedByCategory(int $categoryId): array
    {
        $s = $this->db->prepare(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM ti_blogcms_posts p
             LEFT JOIN ti_blogcms_categories c ON c.id = p.category_id
             WHERE p.status = 'published' AND p.category_id = ?
             ORDER BY COALESCE(p.published_at, p.updated_at) DESC, p.id DESC"
        );
        $s->execute([$categoryId]);
        return $s->fetchAll();
    }

    /** A single PUBLISHED post by slug (null if missing or not published). */
    public function findPublished(string $slug): ?array
    {
        $s = $this->db->prepare(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM ti_blogcms_posts p
             LEFT JOIN ti_blogcms_categories c ON c.id = p.category_id
             WHERE p.slug = ? AND p.status = 'published'"
        );
        $s->execute([$slug]);
        return $s->fetch() ?: null;
    }

    // ── Posts (admin) ────────────────────────────────────────────────────────

    /** All posts (any status), newest first. */
    public function all(): array
    {
        $sql = "SELECT p.*, c.name AS category_name
                FROM ti_blogcms_posts p
                LEFT JOIN ti_blogcms_categories c ON c.id = p.category_id
                ORDER BY p.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $s = $this->db->prepare("SELECT * FROM ti_blogcms_posts WHERE id = ?");
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $slug   = $this->uniqueSlug($data['slug'] ?? $data['title'] ?? '');
        $status = in_array($data['status'] ?? 'draft', ['draft', 'published'], true) ? $data['status'] : 'draft';
        $pubAt  = ($status === 'published') ? ($data['published_at'] ?? date('Y-m-d H:i:s')) : null;

        $s = $this->db->prepare(
            "INSERT INTO ti_blogcms_posts (slug, title, body, excerpt, category_id, status, published_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)"
        );
        $s->execute([
            $slug,
            (string) ($data['title'] ?? ''),
            (string) ($data['body'] ?? ''),
            (string) ($data['excerpt'] ?? ''),
            $data['category_id'] ?? null,
            $status,
            $pubAt,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $current = $this->find($id);
        if (!$current) return;

        $status = in_array($data['status'] ?? 'draft', ['draft', 'published'], true) ? $data['status'] : 'draft';
        $slug   = $this->uniqueSlug($data['slug'] ?? $data['title'] ?? $current['slug'], $id);

        // Set published_at the first time a post becomes published; keep it otherwise.
        $pubAt = $current['published_at'];
        if ($status === 'published' && empty($pubAt)) {
            $pubAt = date('Y-m-d H:i:s');
        }

        $s = $this->db->prepare(
            "UPDATE ti_blogcms_posts
             SET slug = ?, title = ?, body = ?, excerpt = ?, category_id = ?, status = ?, published_at = ?, updated_at = CURRENT_TIMESTAMP
             WHERE id = ?"
        );
        $s->execute([
            $slug,
            (string) ($data['title'] ?? ''),
            (string) ($data['body'] ?? ''),
            (string) ($data['excerpt'] ?? ''),
            $data['category_id'] ?? null,
            $status,
            $pubAt,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $s = $this->db->prepare("DELETE FROM ti_blogcms_posts WHERE id = ?");
        $s->execute([$id]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
        $text = trim($text, '-');
        return $text !== '' ? $text : 'post-' . substr(bin2hex(random_bytes(4)), 0, 8);
    }

    /** Produce a slug unique within ti_blogcms_posts, ignoring $ignoreId (for updates). */
    public function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = $this->slugify($source);
        $slug = $base;
        $n = 1;
        while (true) {
            if ($ignoreId !== null) {
                $s = $this->db->prepare("SELECT id FROM ti_blogcms_posts WHERE slug = ? AND id != ?");
                $s->execute([$slug, $ignoreId]);
            } else {
                $s = $this->db->prepare("SELECT id FROM ti_blogcms_posts WHERE slug = ?");
                $s->execute([$slug]);
            }
            if (!$s->fetch()) return $slug;
            $slug = $base . '-' . (++$n);
        }
    }

    /**
     * Minimal, safe markdown → HTML. Escapes ALL input first (so no raw HTML can
     * pass through), then applies a tiny subset: # / ## / ### headings, **bold**,
     * - / * unordered lists, and blank-line-separated paragraphs.
     */
    public static function mdToHtml(string $md): string
    {
        $md = str_replace(["\r\n", "\r"], "\n", $md);
        $blocks = preg_split('/\n{2,}/', trim($md)) ?: [];
        $html = '';

        foreach ($blocks as $block) {
            $block = trim($block, "\n");
            if ($block === '') continue;
            $lines = explode("\n", $block);

            // Heading: a single-line block starting with 1-3 '#'
            if (count($lines) === 1 && preg_match('/^(#{1,3})\s+(.*)$/', $lines[0], $m)) {
                $level = strlen($m[1]);
                $html .= "<h{$level}>" . self::inline($m[2]) . "</h{$level}>\n";
                continue;
            }

            // Unordered list: every line starts with - or *
            $isList = true;
            foreach ($lines as $ln) {
                if (!preg_match('/^\s*[-*]\s+/', $ln)) { $isList = false; break; }
            }
            if ($isList) {
                $html .= "<ul>\n";
                foreach ($lines as $ln) {
                    $item = preg_replace('/^\s*[-*]\s+/', '', $ln) ?? '';
                    $html .= '<li>' . self::inline($item) . "</li>\n";
                }
                $html .= "</ul>\n";
                continue;
            }

            // Paragraph (single newlines become <br>)
            $html .= '<p>' . nl2br(self::inline($block)) . "</p>\n";
        }

        return $html;
    }

    /** Escape, then apply inline **bold**. */
    private static function inline(string $text): string
    {
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text) ?? $text;
        return $text;
    }
}
