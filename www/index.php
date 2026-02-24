<?php
// www/index.php

// 1. Define Path
define('CORE_PATH', __DIR__ . '/../TiCore');

// 2. Start Session (Required for Login/CSRF)
session_start();

// 3. Load Core Classes manually (Order matters if not using Composer)
require CORE_PATH . '/src/Core/DotEnv.php';
require CORE_PATH . '/config.php'; // Config loads DotEnv
require CORE_PATH . '/src/Core/Logger.php';
require CORE_PATH . '/src/Core/Database.php';
require CORE_PATH . '/src/Core/Security.php';
require CORE_PATH . '/src/Core/Router.php';
require CORE_PATH . '/src/functions_global.php';

// 4. Global Exception Handler (Catch crashes)
set_exception_handler(function($e) {
    \TiCore\Core\Logger::error("Uncaught Exception: " . $e->getMessage());
    // Show a friendly error page if in production
    if (getenv('APP_ENV') !== 'development') {
        http_response_code(500);
        echo "<h1>System Error</h1><p>The administrators have been notified.</p>";
    } else {
        echo "<h1>Error</h1>" . $e->getMessage();
    }
});

// 5. Dispatch
$router = new \TiCore\Core\Router();
$router->dispatch($_SERVER['REQUEST_URI']);