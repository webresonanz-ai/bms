<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use BMS\Router;
use BMS\Controllers\AuthController;
use BMS\Controllers\MembersController;
use BMS\Controllers\EventsController;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$router = new Router();

// CORS preflight
$router->add('OPTIONS', '/{any}', fn() => null, ['cors']);

// Auth routes
$router->add('POST', '/auth/login', [new AuthController(), 'login'], ['cors']);
$router->add('POST', '/auth/register', [new AuthController(), 'register'], ['cors']);
$router->add('GET', '/auth/profile', [new AuthController(), 'profile'], ['cors', 'auth']);
$router->add('PUT', '/auth/profile', [new AuthController(), 'updateProfile'], ['cors', 'auth']);

// Members routes
$router->add('GET', '/members', [new MembersController(), 'index'], ['cors']);
$router->add('GET', '/members/active', [new MembersController(), 'active'], ['cors']);
$router->add('GET', '/members/section', [new MembersController(), 'bySection'], ['cors']);
$router->add('POST', '/members', [new MembersController(), 'store'], ['cors']);
$router->add('POST', '/members/import', [new MembersController(), 'import'], ['cors']);
$router->add('PATCH', '/members/{id}/status', [new MembersController(), 'updateStatus'], ['cors']);
$router->add('DELETE', '/members/{id}', [new MembersController(), 'destroy'], ['cors']);

// Events routes
$router->add('GET', '/events', [new EventsController(), 'index'], ['cors']);
$router->add('GET', '/events/upcoming', [new EventsController(), 'upcoming'], ['cors']);
$router->add('POST', '/events', [new EventsController(), 'store'], ['cors']);
$router->add('DELETE', '/events/{id}', [new EventsController(), 'destroy'], ['cors']);

$router->resolve();