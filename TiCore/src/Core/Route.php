<?php
// TiCore/src/Core/Route.php
namespace TiCore\Core;

/**
 * A single route: HTTP methods, a path pattern compiled to a regex, a handler,
 * an optional name, and a middleware list. Pattern syntax:
 *   /blog/{slug}            required param (one segment)
 *   /post/{id:\d+}          constrained param
 *   /users/{id?}            optional param (preceding slash optional)
 *   /docs/{path:.*}         catch-all (matches unlimited depth — fixes /a/b/c/d)
 */
final class Route
{
    public array $methods;
    public string $pattern;
    public string $regex;
    public array $paramNames = [];
    /** @var string|callable|array */
    public $handler;
    public ?string $name = null;
    public array $middleware = [];

    public function __construct(array $methods, string $pattern, $handler, array $middleware = [])
    {
        $this->methods = array_map('strtoupper', $methods);
        $pattern = '/' . trim($pattern, '/');
        $this->pattern = ($pattern === '/') ? '/' : rtrim($pattern, '/');
        $this->handler = $handler;
        $this->middleware = $middleware;
        $this->compile();
    }

    private function compile(): void
    {
        $pattern = $this->pattern;
        $names = [];
        $regex = '';
        $offset = 0;

        if (preg_match_all(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?(\?)?\}/',
            $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER
        )) {
            foreach ($matches as $m) {
                $full = $m[0][0];
                $start = $m[0][1];
                $name = $m[1][0];
                $constraint = (isset($m[2][0]) && $m[2][0] !== '') ? $m[2][0] : '[^/]+';
                $optional = isset($m[3][0]) && $m[3][0] === '?';

                $literal = substr($pattern, $offset, $start - $offset);
                $names[] = $name;

                if ($optional && str_ends_with($literal, '/')) {
                    // make the preceding "/" optional along with the group
                    $regex .= preg_quote(substr($literal, 0, -1), '#')
                            . '(?:/(?P<' . $name . '>' . $constraint . '))?';
                } else {
                    $regex .= preg_quote($literal, '#')
                            . ($optional
                                ? '(?:(?P<' . $name . '>' . $constraint . '))?'
                                : '(?P<' . $name . '>' . $constraint . ')');
                }
                $offset = $start + strlen($full);
            }
        }
        $regex .= preg_quote(substr($pattern, $offset), '#');
        $this->regex = '#^' . $regex . '$#';
        $this->paramNames = $names;
    }

    public function name(string $name): self { $this->name = $name; return $this; }

    public function middleware($mw): self
    {
        $this->middleware = array_merge($this->middleware, (array) $mw);
        return $this;
    }

    public function methodAllowed(string $method): bool
    {
        return in_array('*', $this->methods, true)
            || in_array(strtoupper($method), $this->methods, true);
    }

    /** @return array<string,string>|false  params on match, false otherwise */
    public function matchPath(string $path)
    {
        if (!preg_match($this->regex, $path, $m)) return false;
        $params = [];
        foreach ($this->paramNames as $n) {
            if (isset($m[$n]) && $m[$n] !== '') {
                $params[$n] = rawurldecode($m[$n]);
            }
        }
        return $params;
    }

    /** Reverse-generate a path from this route's pattern. */
    public function url(array $params = []): string
    {
        $path = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?(\?)?\}/',
            function ($m) use (&$params) {
                $name = $m[1];
                if (array_key_exists($name, $params)) {
                    $v = (string) $params[$name];
                    unset($params[$name]);
                    return implode('/', array_map('rawurlencode', explode('/', $v)));
                }
                return '';
            },
            $this->pattern
        );
        $path = preg_replace('#/{2,}#', '/', $path);
        $path = ($path === '' ) ? '/' : ('/' . trim($path, '/'));
        if ($params) $path .= '?' . http_build_query($params);
        return $path;
    }
}
