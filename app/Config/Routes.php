<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'EmployeeAuthController::showLogin');

// Authentification employe
$routes->get('employee/login', 'EmployeeAuthController::showLogin');
$routes->post('employee/login', 'EmployeeAuthController::login');
$routes->get('employee/dashboard', 'EmployeeController::dashboard');
$routes->get('employee/espace', 'EmployeeController::dashboard');
$routes->post('employee/conges', 'EmployeeController::createConge');
$routes->post('employee/conges/(:num)/cancel', 'EmployeeController::cancelConge/$1');
$routes->post('employee/logout', 'EmployeeAuthController::logout');

// Espace administrateur
$routes->get('admin/dashboard', 'AdminController::dashboard');
$routes->get('admin/employees', 'AdminController::employees');
$routes->post('admin/employees', 'AdminController::createEmployee');
$routes->post('admin/employees/(:num)/deactivate', 'AdminController::deactivateEmployee/$1');
$routes->post('admin/employees/(:num)/activate', 'AdminController::activateEmployee/$1');
$routes->get('admin/departments', 'AdminController::departments');
$routes->post('admin/departments', 'AdminController::createDepartment');
$routes->get('admin/leave-types', 'AdminController::leaveTypes');
$routes->post('admin/leave-types', 'AdminController::createLeaveType');
$routes->get('admin/conges', 'AdminController::conges');
$routes->post('admin/conges/(:num)/statut', 'AdminController::updateCongeStatut/$1');
