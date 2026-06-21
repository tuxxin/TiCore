<?php
// TiCore/src/Core/Router.php
namespace TiCore\Core;

use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;
use TiCore\Core\Middleware\Pipeline;
use TiCore\Core\Middleware\MiddlewareInterface;

/**
 * TiCore v2 router. Backward-compatible 5-tier dispatch:
 *   1. explicit pattern routes (get/post/.../match/any, params, names, middleware)
 *   2. legacy add() exact routes (registered as ANY-method routes)
 *   3. controller auto-discovery  (seg0=Controller, seg1=method, seg2+=args) — unchanged
 *   4. deep, traversal-guarded view fallback (templates/default/<path>.php)
 *   5. 404
 * Keeps the framework dependency-free and SEO-first.
 */
class Router
{
    /** @var Route[] */
    protected array $routes = [];
    /** @var array<string,Route> */
    protected array $named = [];
    /** @var array<string,string> alias => MiddlewareInterface class */
    protected array $mwAliases = [];
    /** @var array<int,array{prefix:string,middleware:array}> */
    protected array $groupStack = [];
    /** @var array middleware (aliases/instances) run on every matched route */
    protected array $globalMiddleware = [];

    protected static ?Router $instance = null;

    public function __construct()
    {
        self::$instance = $this;
    }

    public static function instance(): ?Router { return self::$instance; }

    // ── Registration ────────────────────────────────────────────────────────
    public function get(string $p, $h, array $mw = []): Route    { return $this->map(['GET', 'HEAD'], $p, $h, $mw); }
    public function post(string $p, $h, array $mw = []): Route   { return $this->map(['POST'], $p, $h, $mw); }
    public function put(string $p, $h, array $mw = []): Route    { return $this->map(['PUT'], $p, $h, $mw); }
    public function patch(string $p, $h, array $mw = []): Route  { return $this->map(['PATCH'], $p, $h, $mw); }
    public function delete(string $p, $h, array $mw = []): Route { return $this->map(['DELETE'], $p, $h, $mw); }
    public function any(string $p, $h, array $mw = []): Route    { return $this->map(['*'], $p, $h, $mw); }
    public function match(array $methods, string $p, $h, array $mw = []): Route { return $this->map($methods, $p, $h, $mw); }

    /** Legacy API — kept exactly: add('docs/cli','DocsController@cli'). Optional middleware added. */
    public function add(string $route, string $controller, array $middleware = []): Route
    {
        return $this->map(['*'], $route, $controller, $middleware);
    }

    protected function map(array $methods, string $pattern, $handler, array $mw = []): Route
    {
        $prefix = '';
        $groupMw = [];
        foreach ($this->groupStack as $g) {
            $prefix .= '/' . trim($g['prefix'] ?? '', '/');
            $groupMw = array_merge($groupMw, $g['middleware'] ?? []);
        }
        $full = '/' . trim(trim($prefix, '/') . '/' . trim($pattern, '/'), '/');
        $route = new Route($methods, $full, $handler, array_merge($groupMw, $mw));
        $this->routes[] = $route;
        return $route;
    }

    public function group(array $opts, callable $fn): void
    {
        $this->groupStack[] = $opts;
        $fn($this);
        array_pop($this->groupStack);
    }

    public function aliasMiddleware(string $alias, string $class): void { $this->mwAliases[$alias] = $class; }
    public function globalMiddleware(array $mw): void { $this->globalMiddleware = $mw; }

    /** Load a route file with $router in scope. */
    public function loadRoutes(string $file): void
    {
        $router = $this;
        (static function () use ($router, $file) { require $file; })();
    }

    /** Reverse-generate an absolute URL for a named route. */
    public function routeUrl(string $name, array $params = []): string
    {
        if (empty($this->named)) {
            foreach ($this->routes as $r) { if ($r->name) $this->named[$r->name] = $r; }
        }
        $r = $this->named[$name] ?? null;
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
        return $r ? $base . $r->url($params) : ($base . '/');
    }

    // ── Dispatch ──────────────────────────────────────────────────────────────
    public function dispatch(string $uri): void
    {
        $this->sendBaseHeaders();

        $req = Request::capture();
        $path = $req->path;
        $method = $req->method;

        // TIER 1 + 2: explicit / legacy routes
        $methodMismatch = false;
        foreach ($this->routes as $route) {
            $params = $route->matchPath($path);
            if ($params === false) continue;
            if (!$route->methodAllowed($method)) { $methodMismatch = true; continue; }

            $req->params = $params;
            $mw = $this->resolveMiddleware(array_merge($this->globalMiddleware, $route->middleware));
            $response = Pipeline::run($req, $mw, fn(Request $r) => $this->invoke($route->handler, $r));
            $response->send();
            return;
        }
        if ($methodMismatch) {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        // TIER 3: controller auto-discovery (legacy behavior)
        if ($this->autoDiscover($path)) return;

        // TIER 4: deep, traversal-guarded view fallback
        if ($this->viewFallback($path)) return;

        // TIER 5
        http_response_code(404);
        \view('404', ['title' => 'Page Not Found']);
    }

    protected function sendBaseHeaders(): void
    {
        if (headers_sent()) return;
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    }

    /** @return MiddlewareInterface[] */
    protected function resolveMiddleware(array $names): array
    {
        $out = [];
        foreach ($names as $n) {
            if ($n instanceof MiddlewareInterface) { $out[] = $n; continue; }
            $class = $this->mwAliases[$n] ?? $n;
            if (is_string($class) && class_exists($class)) {
                $inst = new $class();
                if ($inst instanceof MiddlewareInterface) $out[] = $inst;
            }
        }
        return $out;
    }

    /** Run a route handler, capturing any echoed output into the Response. */
    protected function invoke($handler, Request $req): Response
    {
        ob_start();
        try {
            $result = $this->callHandler($handler, $req);
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        $echoed = ob_get_clean();

        if ($result instanceof Response) return $result;
        if (is_string($result)) return Response::make($echoed . $result);
        return Response::make($echoed);
    }

    protected function callHandler($handler, Request $req)
    {
        if (is_string($handler) && str_contains($handler, '@')) {
            [$ctrl, $m] = explode('@', $handler, 2);
            $handler = [$this->resolveController($ctrl), $m];
        }
        if (!is_callable($handler)) {
            throw new \RuntimeException('Invalid route handler');
        }
        return $this->callWithParams($handler, $req);
    }

    protected function resolveController(string $name)
    {
        if (class_exists($name)) return new $name();
        if (!preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $name)) {
            throw new \RuntimeException('Invalid controller name');
        }
        $file = CORE_PATH . '/src/Controllers/' . $name . '.php';
        if (is_file($file)) require_once $file;
        $ns = defined('CONTROLLER_NS') ? CONTROLLER_NS : 'TiCore\\Controllers';
        $fq = $ns . '\\' . $name;
        if (!class_exists($fq)) throw new \RuntimeException("Controller not found: $name");
        return new $fq();
    }

    protected function callWithParams(callable $cb, Request $req)
    {
        try {
            $ref = is_array($cb)
                ? new \ReflectionMethod($cb[0], $cb[1])
                : new \ReflectionFunction($cb);
            $params = $ref->getParameters();
            if (!empty($params)) {
                $t = $params[0]->getType();
                if ($t instanceof \ReflectionNamedType && !$t->isBuiltin()
                    && ltrim($t->getName(), '\\') === Request::class) {
                    return $cb($req);
                }
            }
        } catch (\ReflectionException $e) {
            // fall through to positional
        }
        return $cb(...array_values($req->params));
    }

    /** Legacy controller auto-discovery (seg0=Controller, seg1=method, rest=args). */
    protected function autoDiscover(string $path): bool
    {
        $segments = ($path === '/') ? ['home'] : explode('/', trim($path, '/'));
        foreach ($segments as $seg) {
            if (!preg_match('/^[a-z0-9][a-z0-9\-]*$/i', $seg)) return false;
        }
        $controllerName = str_replace('-', '', ucwords($segments[0], '-')) . 'Controller';
        $file = CORE_PATH . '/src/Controllers/' . $controllerName . '.php';
        if (!is_file($file)) return false;
        require_once $file;
        $ns = defined('CONTROLLER_NS') ? CONTROLLER_NS : 'TiCore\\Controllers';
        $fq = $ns . '\\' . $controllerName;
        if (!class_exists($fq)) return false;
        $controller = new $fq();

        if (isset($segments[1])) {
            $method = lcfirst(str_replace('-', '', ucwords($segments[1], '-')));
            if (method_exists($controller, $method)) {
                $controller->$method(...array_slice($segments, 2));
                return true;
            }
            $controller->index(...array_slice($segments, 1));
            return true;
        }
        $controller->index();
        return true;
    }

    /** Arbitrary-depth view fallback, guarded against path traversal. */
    protected function viewFallback(string $path): bool
    {
        $rel = trim($path, '/');
        if ($rel === '') $rel = 'home';
        if (str_contains($rel, '..') || str_contains($rel, "\0")) return false;
        foreach (explode('/', $rel) as $seg) {
            if ($seg === '' || !preg_match('/^[a-z0-9][a-z0-9\-]*$/i', $seg)) return false;
        }
        $base = realpath(CORE_PATH . '/templates/default');
        if ($base === false) return false;
        $real = realpath($base . '/' . $rel . '.php');
        if ($real === false || !str_starts_with($real, $base . DIRECTORY_SEPARATOR)) return false;

        require_once CORE_PATH . '/src/Controllers/PageController.php';
        (new \TiCore\Controllers\PageController())->show($rel);
        return true;
    }
}
