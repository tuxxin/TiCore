<?php
/** payments addon routes. @var \TiCore\Core\Router $router */
$c = 'Tuxxin\\TiCore\\Addons\\Payments\\Controllers\\CheckoutController';

$router->get('/pay',                  $c . '@demo')->name('pay.demo');
$router->post('/pay/stripe/checkout', $c . '@stripeCheckout', ['csrf'])->name('pay.stripe');
$router->get('/pay/success',          $c . '@success')->name('pay.success');
$router->post('/pay/stripe/webhook',  $c . '@stripeWebhook');   // Stripe-signed; no CSRF
$router->post('/pay/paypal/create',   $c . '@paypalCreate');    // PayPal JS SDK (AJAX)
$router->post('/pay/paypal/capture',  $c . '@paypalCapture');
