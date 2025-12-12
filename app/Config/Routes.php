<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');
$routes->match(['get', 'post'], '/register', 'AuthController::register');
$routes->match(['get', 'post'], '/login', 'AuthController::login');

$routes->get('/logout', 'AuthController::logout');

$routes->get('/dashboard', 'DashboardController::index');
$routes->post('/upload', 'DashboardController::upload');
$routes->get('/download/(:num)', 'DashboardController::download/$1');
