<?php
/**
 * rest-api-kit admin routes. @var \TiCore\Core\Router $router
 * 'auth' + 'csrf' aliases are provided by core.
 */
$c = 'Tuxxin\\TiCore\\Addons\\RestApiKit\\Controllers\\ApiAdminController';

$router->group(['prefix' => 'admin', 'middleware' => ['auth']], function ($router) use ($c) {
    // Key manager
    $router->get('/api-keys',              $c . '@keysIndex')->name('restapikit.keys');
    $router->post('/api-keys',             $c . '@keysCreate', ['csrf']);
    $router->post('/api-keys/revoke/{id:\d+}', $c . '@keysRevoke', ['csrf'])->name('restapikit.keys.revoke');

    // Live console (server-side proxy keeps the key off the browser)
    $router->get('/api-console',           $c . '@console')->name('restapikit.console');
    $router->post('/api-console/proxy',    $c . '@consoleProxy', ['csrf'])->name('restapikit.console.proxy');
});
