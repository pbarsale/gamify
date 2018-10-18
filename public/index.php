<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

ini_set('session.cookie.lifetime','864000');	// 10 days in seconds

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Session
 */
session_start();
/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', array('controller' => 'Home', 'action' => 'index'));
$router->add('login', array('controller' => 'Login', 'action' => 'new'));
$router->add('logout', array('controller' => 'Login', 'action' => 'destroy'));
$router->add('password/reset/{token:[\da-f]+}', array('controller' => 'Password', 'action' => 'reset'));
$router->add('{controller}/{action}');

// Routes for Admin
$router->add('admin', array('controller' => 'Login', 'action' => 'newAdmin'));

$router->dispatch($_SERVER['QUERY_STRING']);
