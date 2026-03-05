<?php
// TiCore/src/Core/Router.php
namespace TiCore\Core;

class Router {
    protected $routes = [];

    // Manual Route Registration
    public function add($route, $controller) {
        $this->routes[$route] = $controller;
    }

    public function dispatch($uri) {
        // Clean URI
        $uri = trim(parse_url($uri, PHP_URL_PATH), '/');
        if ($uri === '') $uri = 'home'; // Default to home

        // Reject URIs that could traverse directories or contain unexpected chars
        if (!preg_match('/^[a-z0-9][a-z0-9\-]*$/i', $uri)) {
            http_response_code(400);
            return;
        }

        // CASE A: Manual Routes
        if (array_key_exists($uri, $this->routes)) {
            $this->callController($this->routes[$uri]);
            return;
        }

        // CASE B: Automated Controller (e.g., /login -> LoginController)
        $controllerName = str_replace('-', '', ucwords($uri, '-')) . 'Controller';
        $controllerPath = CORE_PATH . '/src/Controllers/' . $controllerName . '.php';

        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $fullClassName = "TiCore\\Controllers\\$controllerName";
            $controller = new $fullClassName();
            $controller->index();
            return;
        }

        // CASE C (Fallback): No Controller, but View exists? (e.g., /compare -> compare.php)
        $viewPath = CORE_PATH . '/templates/default/' . $uri . '.php';
        if (file_exists($viewPath)) {
            require_once CORE_PATH . '/src/Controllers/PageController.php';
            $controller = new \TiCore\Controllers\PageController();
            $controller->show($uri);
            return;
        }

        // CASE 404: Not Found
        http_response_code(404);
        view('404', ['title' => 'Page Not Found']);
    }

    protected function callController(string $name): void {
        // Validate name to prevent path traversal via manually registered routes
        if (!preg_match('/^[A-Za-z][A-Za-z0-9]*Controller$/', $name)) {
            http_response_code(400);
            return;
        }
        require_once CORE_PATH . '/src/Controllers/' . $name . '.php';
        $class = "TiCore\\Controllers\\$name";
        $controller = new $class();
        $controller->index();
    }
}