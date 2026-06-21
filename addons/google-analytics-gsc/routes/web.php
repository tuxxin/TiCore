<?php
/**
 * google-analytics-gsc addon routes. @var \TiCore\Core\Router $router
 */
$c = 'Tuxxin\\TiCore\\Addons\\GoogleAnalyticsGsc\\Controllers\\SeoStatsController';

// Admin (auth-gated).
$router->group(['prefix' => 'admin', 'middleware' => ['auth']], function ($router) use ($c) {
    $router->get('/seo-stats',       $c . '@index')->name('seo-stats.index');
    $router->get('/seo-stats/setup', $c . '@setup')->name('seo-stats.setup');
});
