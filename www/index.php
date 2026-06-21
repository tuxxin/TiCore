<?php
// www/index.php

// 1. Define Path
define('CORE_PATH', __DIR__ . '/../TiCore');

// 2. Enable error display early so any bootstrap failures are visible.
//    config.php will override these settings once APP_ENV is known.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 3. Harden + start session (HttpOnly always; Secure when on HTTPS; SameSite=Lax).
$__https = (($_SERVER['HTTPS'] ?? '') === 'on')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443);
session_set_cookie_params([
    'httponly' => true,
    'secure'   => $__https,
    'samesite' => 'Lax',
]);
session_start();

// 4. Load Core Classes (order matters — no Composer autoload yet)
require CORE_PATH . '/src/Core/DotEnv.php';
require CORE_PATH . '/config.php';       // sets LOG_LEVEL, error_reporting, display_errors
require CORE_PATH . '/src/Core/Logger.php';
require CORE_PATH . '/src/Core/Database.php';
require CORE_PATH . '/src/Core/Security.php';
require CORE_PATH . '/src/Core/Http/Request.php';
require CORE_PATH . '/src/Core/Http/Response.php';
require CORE_PATH . '/src/Core/Middleware/MiddlewareInterface.php';
require CORE_PATH . '/src/Core/Middleware/Pipeline.php';
require CORE_PATH . '/src/Core/Middleware/CsrfMiddleware.php';
require CORE_PATH . '/src/Core/Middleware/AuthMiddleware.php';
require CORE_PATH . '/src/Core/Route.php';
require CORE_PATH . '/src/Core/Router.php';
require CORE_PATH . '/src/functions_global.php';

// 5. Global exception handler
set_exception_handler(function (\Throwable $e) {
    \TiCore\Core\Logger::critical(
        get_class($e) . ': ' . $e->getMessage() .
        ' in ' . $e->getFile() . ':' . $e->getLine()
    );

    http_response_code(500);

    if (getenv('APP_ENV') === 'development') {
        echo '<div style="font-family:monospace;background:#fff3cd;border:2px solid #ffc107;'
           . 'padding:24px;margin:24px;border-radius:6px;max-width:100%;overflow:auto;">';
        echo '<h2 style="color:#856404;margin-top:0;">&#9888; ' . htmlspecialchars(get_class($e)) . '</h2>';
        echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile())
           . ' &nbsp;<strong>Line:</strong> ' . (int)$e->getLine() . '</p>';
        echo '<h3 style="margin-bottom:4px;">Stack Trace</h3>';
        echo '<pre style="background:#f8f9fa;padding:12px;overflow:auto;border-radius:4px;">'
           . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    } else {
        echo '<h1>System Error</h1><p>The administrators have been notified.</p>';
    }
});

// 6. PHP error handler — routes PHP warnings/notices/deprecations through Logger
set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    $levelMap = [
        E_ERROR             => \TiCore\Core\Logger::LEVEL_CRITICAL,
        E_PARSE             => \TiCore\Core\Logger::LEVEL_CRITICAL,
        E_CORE_ERROR        => \TiCore\Core\Logger::LEVEL_CRITICAL,
        E_COMPILE_ERROR     => \TiCore\Core\Logger::LEVEL_CRITICAL,
        E_USER_ERROR        => \TiCore\Core\Logger::LEVEL_ERROR,
        E_WARNING           => \TiCore\Core\Logger::LEVEL_WARNING,
        E_CORE_WARNING      => \TiCore\Core\Logger::LEVEL_WARNING,
        E_COMPILE_WARNING   => \TiCore\Core\Logger::LEVEL_WARNING,
        E_USER_WARNING      => \TiCore\Core\Logger::LEVEL_WARNING,
        E_NOTICE            => \TiCore\Core\Logger::LEVEL_WARNING,
        E_USER_NOTICE       => \TiCore\Core\Logger::LEVEL_WARNING,
        E_DEPRECATED        => \TiCore\Core\Logger::LEVEL_DEPRECATED,
        E_USER_DEPRECATED   => \TiCore\Core\Logger::LEVEL_DEPRECATED,
    ];
    \TiCore\Core\Logger::log("$errstr in $errfile:$errline", $levelMap[$errno] ?? \TiCore\Core\Logger::LEVEL_WARNING);
    return false;
});

// 7. Build router, register built-in middleware aliases, load routes, dispatch.
$router = new \TiCore\Core\Router();
$router->aliasMiddleware('csrf', \TiCore\Core\Middleware\CsrfMiddleware::class);
$router->aliasMiddleware('auth', \TiCore\Core\Middleware\AuthMiddleware::class);

// Addon loader hook (Phase 1d) — boots installed addons if present.
if (is_file(CORE_PATH . '/src/Core/AddonManager.php')) {
    require CORE_PATH . '/src/Core/AddonInterface.php';
    require CORE_PATH . '/src/Core/AddonContext.php';
    require CORE_PATH . '/src/Core/AddonManager.php';
    (new \TiCore\Core\AddonManager(CORE_PATH . '/addons'))->boot($router);
}

if (is_file(CORE_PATH . '/routes/web.php')) $router->loadRoutes(CORE_PATH . '/routes/web.php');
if (is_file(CORE_PATH . '/routes/api.php')) $router->loadRoutes(CORE_PATH . '/routes/api.php');

$router->dispatch($_SERVER['REQUEST_URI']);
