<?php
// TiCore/src/Core/Http/Request.php
namespace TiCore\Core\Http;

/**
 * Lightweight, lazy request wrapper over the superglobals. Optional: legacy
 * controllers keep using $_GET/$_POST directly; modern handlers type-hint Request.
 */
final class Request
{
    public string $method = 'GET';
    public string $path = '/';
    /** @var array<string,string> route params filled by the Router after a match */
    public array $params = [];

    private array $query = [];
    private array $body = [];
    private array $server = [];
    private array $cookies = [];
    private ?array $json = null;

    public static function capture(): self
    {
        $r = new self();
        $r->server  = $_SERVER;
        $r->query   = $_GET;
        $r->body    = $_POST;
        $r->cookies = $_COOKIE;

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        // Method override (HTML forms only do GET/POST) — gated to POST origin.
        if ($method === 'POST') {
            $override = $_POST['_method'] ?? ($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? '');
            if ($override !== '') {
                $override = strtoupper($override);
                if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                    $method = $override;
                }
            }
        }
        $r->method = $method;

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $r->path = ($path === '/') ? '/' : '/' . trim($path, '/');
        return $r;
    }

    public function param(string $k, $default = null) { return $this->params[$k] ?? $default; }

    public function query(?string $k = null, $default = null)
    {
        return $k === null ? $this->query : ($this->query[$k] ?? $default);
    }

    public function input(?string $k = null, $default = null)
    {
        $all = array_merge($this->query, $this->body, $this->jsonBody());
        return $k === null ? $all : ($all[$k] ?? $default);
    }

    public function all(): array { return array_merge($this->query, $this->body, $this->jsonBody()); }

    private function jsonBody(): array
    {
        if ($this->json === null) {
            $this->json = $this->isJson()
                ? (json_decode(file_get_contents('php://input') ?: '', true) ?: [])
                : [];
        }
        return $this->json;
    }

    public function isJson(): bool
    {
        return str_contains(strtolower($this->server['CONTENT_TYPE'] ?? ''), 'application/json');
    }

    public function wantsJson(): bool
    {
        return $this->isJson()
            || str_contains($this->server['HTTP_ACCEPT'] ?? '', 'application/json')
            || str_starts_with($this->path, '/api/');
    }

    public function header(string $name): ?string
    {
        $k = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->server[$k] ?? null;
    }

    public function bearerToken(): ?string
    {
        $h = $this->header('Authorization') ?? '';
        return preg_match('/Bearer\s+(\S+)/i', $h, $m) ? $m[1] : null;
    }

    /** Cloudflare-aware real client IP. */
    public function ip(): string
    {
        if (!empty($this->server['HTTP_CF_CONNECTING_IP'])) return $this->server['HTTP_CF_CONNECTING_IP'];
        if (!empty($this->server['HTTP_X_FORWARDED_FOR'])) return trim(explode(',', $this->server['HTTP_X_FORWARDED_FOR'])[0]);
        return $this->server['REMOTE_ADDR'] ?? '';
    }
}
