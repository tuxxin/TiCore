<?php
/**
 * contact-mailer addon routes. @var \TiCore\Core\Router $router
 */
$c = 'Tuxxin\\TiCore\\Addons\\ContactMailer\\Controllers\\ContactController';

$router->get('/contact',         $c . '@show')->name('contact');
$router->post('/contact/submit', $c . '@submit', ['csrf'])->name('contact.submit');
$router->get('/contact/thanks',  $c . '@thanks')->name('contact.thanks');
