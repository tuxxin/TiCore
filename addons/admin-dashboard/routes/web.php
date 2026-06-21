<?php
/**
 * admin-dashboard addon routes. @var \TiCore\Core\Router $router
 * 'auth' middleware alias is provided by core (TiCore\Core\Middleware\AuthMiddleware).
 */
$c = 'Tuxxin\\TiCore\\Addons\\AdminDashboard\\Controllers\\DashboardController';

$router->group(['prefix' => 'admin', 'middleware' => ['auth']], function ($router) use ($c) {
    $router->get('/', $c . '@index')->name('admin.dashboard');
});
