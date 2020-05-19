<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';
/**
 * Sessions
 */
session_start();

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Routing
 */
$router = new Core\Router();

// Add the routes

$router->add('{controller}/{action}');
$router->add('home', ['controller' => 'Home', 'action' => 'index']);
$router->add('admin/{controller}/{action}',['namespace' => 'Admin']);

$router->add('{controller}/{id:[\d]+}/{action}');
    
$router->dispatch($_SERVER['QUERY_STRING']);
