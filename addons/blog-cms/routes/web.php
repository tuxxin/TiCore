<?php
/**
 * blog-cms addon routes. @var \TiCore\Core\Router $router
 */
$pub = 'Tuxxin\\TiCore\\Addons\\BlogCms\\Controllers\\BlogController';
$adm = 'Tuxxin\\TiCore\\Addons\\BlogCms\\Controllers\\BlogAdminController';

// ── Public ──────────────────────────────────────────────────────────────────
$router->get('/blog',                  $pub . '@index')->name('blog.index');
$router->get('/blog/category/{slug}',  $pub . '@category')->name('blog.category');
$router->get('/blog/{slug}',           $pub . '@show')->name('blog.show');

// ── Admin (auth-gated; writes also CSRF-checked in-controller) ────────────────
$router->group(['prefix' => 'admin', 'middleware' => ['auth']], function ($router) use ($adm) {
    $router->get('/blog',              $adm . '@index')->name('blog.admin.index');
    $router->get('/blog/new',          $adm . '@create')->name('blog.admin.new');
    $router->post('/blog',             $adm . '@store',  ['csrf'])->name('blog.admin.store');
    $router->get('/blog/edit/{id:\d+}',     $adm . '@edit')->name('blog.admin.edit');
    $router->post('/blog/update/{id:\d+}',  $adm . '@update', ['csrf'])->name('blog.admin.update');
    $router->post('/blog/delete/{id:\d+}',  $adm . '@delete', ['csrf'])->name('blog.admin.delete');
});
