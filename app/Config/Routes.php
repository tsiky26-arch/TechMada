<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Authentification employe (sans base de donnees pour le moment)
$routes->get('employee/login', 'EmployeeAuthController::showLogin');
$routes->post('employee/login', 'EmployeeAuthController::login');
$routes->get('employee/dashboard', 'EmployeeAuthController::dashboard');
$routes->post('employee/logout', 'EmployeeAuthController::logout');
