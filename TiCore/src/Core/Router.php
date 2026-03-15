<?php
// TiCore/src/Core/Router.php
namespace TiCore\Core;

class Router {
    protected array $routes = [];

    /**
     * Register a manual route.
     *
     * @param string $route      URI path (with or without leading slash). e.g. '/docs/wireguard'
     * @param string $controller Controller class name, optionally with '@method'.
     *                           e.g. 'DocsController' or 'DocsController@wireguard'
     */
    public function add(string $route, string $controller): void {
        $this->routes[trim($route, '/')] = $controller;
    }

    public function dispatch(string $uri): void {
        $path = trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');

        // Split into segments; empty path defaults to ['home']
        $segments = $path !== '' ? explode('/', $path) : ['home'];

        // Validate every segment — no dots, no slashes, no special chars
        foreach ($segments as $seg) {
            if (!preg_match('/^[a-z0-9][a-z0-9\-]*$/i', $seg)) {
                http_response_code(400);
                return;
            }
        }

        $fullPath = implode('/', $segments); // e.g. 'docs/wireguard'

        // ── CASE A: Manual route — exact match ─────────────────────────────────
        if (array_key_exists($fullPath, $this->routes)) {
            [$controllerName, $method] = $this->parseRouteTarget($this->routes[$fullPath]);
            $this->callController($controllerName, $method);
            return;
        }

        // ── CASE B: Auto-discovery ─────────────────────────────────────────────
        //   seg[0] → ControllerName, seg[1] → method (slug → camelCase), seg[2+] → params
        $controllerName = str_replace('-', '', ucwords($segments[0], '-')) . 'Controller';
        $controllerPath = CORE_PATH . '/src/Controllers/' . $controllerName . '.php';

        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $fullClass  = "TiCore\\Controllers\\$controllerName";
            $controller = new $fullClass();

            if (isset($segments[1])) {
                // Convert slug to camelCase: 'my-action' → 'myAction'
                $method = lcfirst(str_replace('-', '', ucwords($segments[1], '-')));
                $params = array_slice($segments, 2);

                if (method_exists($controller, $method)) {
                    $controller->$method(...$params);
                    return;
                }

                // Method not found — fall back to index() with remaining segments as args
                $controller->index(...array_slice($segments, 1));
            } else {
                $controller->index();
            }
            return;
        }

        // ── CASE C: View fallback ─────────────────────────────────────────────
        //   Supports flat (/compare → compare.php) and nested (/docs/cli → docs/cli.php)
        $viewPath = CORE_PATH . '/templates/default/' . $fullPath . '.php';
        if (file_exists($viewPath)) {
            require_once CORE_PATH . '/src/Controllers/PageController.php';
            $controller = new \TiCore\Controllers\PageController();
            $controller->show($fullPath);
            return;
        }

        // ── CASE 404 ──────────────────────────────────────────────────────────
        http_response_code(404);
        view('404', ['title' => 'Page Not Found']);
    }

    /**
     * Parse "ControllerName" or "ControllerName@method" into [name, method].
     */
    private function parseRouteTarget(string $target): array {
        if (str_contains($target, '@')) {
            [$controller, $method] = explode('@', $target, 2);
            return [$controller, $method];
        }
        return [$target, 'index'];
    }

    protected function callController(string $name, string $method = 'index', array $params = []): void {
        // Validate class name to prevent path traversal via manually registered routes
        if (!preg_match('/^[A-Za-z][A-Za-z0-9]*Controller$/', $name)) {
            http_response_code(400);
            return;
        }
        require_once CORE_PATH . '/src/Controllers/' . $name . '.php';
        $class = "TiCore\\Controllers\\$name";
        $controller = new $class();
        $controller->$method(...$params);
    }
}
