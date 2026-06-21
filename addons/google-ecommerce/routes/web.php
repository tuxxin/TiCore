<?php
/**
 * google-ecommerce addon routes. @var \TiCore\Core\Router $router
 */
$c = 'Tuxxin\\TiCore\\Addons\\GoogleEcommerce\\Controllers\\FeedController';

// Public: the Merchant Center product feed.
$router->get('/feeds/merchant.xml', $c . '@feed')->name('merchant.feed');

// Admin (auth-gated): setup guide.
$router->group(['prefix' => 'admin', 'middleware' => ['auth']], function ($router) use ($c) {
    $router->get('/google-ecommerce', $c . '@admin')->name('google-ecommerce.admin');
});
