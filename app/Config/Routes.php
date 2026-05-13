<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Authentification RH
$routes->get('/', 'EmployeeAuthController::showLogin');
$routes->get('login', 'EmployeeAuthController::showLogin');
$routes->post('login', 'EmployeeAuthController::login');
$routes->get('dashboard', 'EmployeeAuthController::dashboard');
$routes->post('logout', 'EmployeeAuthController::logout');
$routes->get('rh', 'RhController::index');
$routes->post('rh/conges/(:num)/approuver', 'RhController::approve/$1');
$routes->post('rh/conges/(:num)/refuser', 'RhController::refuse/$1');
$routes->post('rh/conges/(:num)/annuler', 'RhController::cancel/$1');

// Alias employe conserves pour les liens existants.
$routes->get('employee/login', 'EmployeeAuthController::showLogin');
$routes->post('employee/login', 'EmployeeAuthController::login');
$routes->get('employee/dashboard', 'EmployeeAuthController::dashboard');
$routes->post('employee/logout', 'EmployeeAuthController::logout');
