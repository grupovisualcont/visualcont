<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('hello', 'Home::hello');

$routes->group('app', static function($routes) {

    $routes->group('purchase', static function($routes) {

        $routes->get('create', 'Compras::crear');
    });
});