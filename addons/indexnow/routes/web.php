<?php
/**
 * indexnow addon routes. @var \TiCore\Core\Router $router
 */
$c = 'Tuxxin\\TiCore\\Addons\\Indexnow\\Controllers\\IndexNowController';

// Public: serve the verification key file at the site root.
$router->get('/indexnow-key.txt', $c . '@keyFile')->name('indexnow.key');

// Admin (auth-gated).
$router->group(['prefix' => 'admin', 'middleware' => ['auth']], function ($router) use ($c) {
    $router->get('/indexnow',         $c . '@admin')->name('indexnow.admin');
    $router->post('/indexnow/submit', $c . '@submit', ['csrf'])->name('indexnow.submit');
});
