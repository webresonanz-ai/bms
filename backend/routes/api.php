<?php

/**
 * API Router — maps URI + method combinations to controller actions.
 * Batavia Madrigal Singers — Backend API
 *
 * Supported routes:
 *   POST   /api/v1/auth/register
 *   POST   /api/v1/auth/login
 *   GET    /api/v1/auth/me
 */

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../helpers/response.php';

$method = $_SERVER['REQUEST_METHOD'];

// Strip query string and normalise slashes
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$uri = '/' . trim($uri, '/');

// ------------------------------------------------------------------
// Route definitions
// ------------------------------------------------------------------

$routes = [
    'POST /api/v1/auth/register' => [AuthController::class, 'register'],
    'POST /api/v1/auth/login'    => [AuthController::class, 'login'],
    'GET /api/v1/auth/me'        => [AuthController::class, 'me'],
];

$key = "$method $uri";

if (isset($routes[$key])) {
    [$class, $action] = $routes[$key];
    (new $class())->$action();
} else {
    respondError("Route not found: $method $uri", 404);
}
