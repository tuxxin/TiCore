<?php
/**
 * rest-api-kit API routes. @var \TiCore\Core\Router $router
 * 'apikey' alias is registered by RestApiKitAddon::register().
 */
$c = 'Tuxxin\\TiCore\\Addons\\RestApiKit\\Controllers\\ApiController';

$router->group(['prefix' => 'api/v2', 'middleware' => ['apikey']], function ($router) use ($c) {
    $router->get('/ping',    $c . '@ping')->name('restapikit.ping');
    $router->get('/whoami',  $c . '@whoami')->name('restapikit.whoami');
});
