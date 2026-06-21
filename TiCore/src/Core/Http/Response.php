<?php
// TiCore/src/Core/Http/Response.php
namespace TiCore\Core\Http;

/**
 * Minimal response value object. Handlers may return one (status/headers/body)
 * or just echo (legacy) — the Router captures echoed output into a Response.
 */
final class Response
{
    private int $status = 200;
    private array $headers = [];
    private string $body = '';

    public static function make(string $body = '', int $status = 200): self
    {
        $r = new self();
        $r->body = $body;
        $r->status = $status;
        return $r;
    }

    public static function json($data, int $status = 200): self
    {
        $r = new self();
        $r->status = $status;
        $r->headers['Content-Type'] = 'application/json; charset=utf-8';
        $r->body = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return $r;
    }

    public static function redirect(string $to, int $status = 302): self
    {
        $r = new self();
        $r->status = $status;
        $r->headers['Location'] = $to;
        return $r;
    }

    /** Render a template via the global view() helper into a Response body. */
    public static function view(string $name, array $data = [], int $status = 200): self
    {
        ob_start();
        \view($name, $data);
        return self::make(ob_get_clean(), $status);
    }

    public function withHeader(string $k, string $v): self { $this->headers[$k] = $v; return $this; }
    public function status(int $s): self { $this->status = $s; return $this; }
    public function getStatus(): int { return $this->status; }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->status);
            foreach ($this->headers as $k => $v) {
                header($k . ': ' . $v);
            }
        }
        echo $this->body;
    }
}
