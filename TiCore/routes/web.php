<?php
/**
 * TiCore/routes/web.php — declare your web routes here ($router is in scope).
 * Optional: with no routes, the framework still serves controllers
 * (src/Controllers/XController.php) and templates (templates/default/<path>.php).
 *
 * @var \TiCore\Core\Router $router
 */

use TiCore\Core\Http\Request;
use TiCore\Core\Http\Response;

// Parameterized route (type-hint Request to receive it; else params are positional):
// $router->get('/hello/{name}', fn(Request $r) =>
//     Response::make('Hello, ' . e($r->param('name'))))->name('hello');

// Constrained param + named route:
// $router->get('/post/{id:\d+}', 'PostController@show')->name('post.show');

// Unlimited-depth catch-all:
// $router->get('/docs/{path:.*}', 'DocsController@show');

// HTTP-method routing + middleware:
// $router->post('/contact', 'ContactController@submit', ['csrf']);

// Route groups (shared prefix + middleware):
// $router->group(['prefix' => 'admin', 'middleware' => ['auth']], function ($router) {
//     $router->get('/dashboard', 'Admin\DashboardController@index');
// });
