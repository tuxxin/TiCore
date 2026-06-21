<?php
/**
 * auth addon routes. @var \TiCore\Core\Router $router
 */
$c = 'Tuxxin\\TiCore\\Addons\\Auth\\Controllers\\AuthController';

$router->get('/login',     $c . '@showLogin',    ['guest'])->name('login');
$router->post('/login',    $c . '@login',        ['guest']);
$router->get('/register',  $c . '@showRegister', ['guest'])->name('register');
$router->post('/register', $c . '@register',     ['guest']);
$router->post('/logout',   $c . '@logout',       ['auth'])->name('logout');
$router->get('/account',   $c . '@account',      ['auth'])->name('account');
