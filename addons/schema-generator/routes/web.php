<?php
/**
 * schema-generator addon routes. @var \TiCore\Core\Router $router
 * Public, stateless tool — POST is CSRF-guarded; the controller also checks a honeypot.
 */
$c = 'Tuxxin\\TiCore\\Addons\\SchemaGenerator\\Controllers\\SchemaController';

$router->get('/tools/schema',  $c . '@show')->name('schema.show');
$router->post('/tools/schema', $c . '@generate', ['csrf'])->name('schema.generate');
