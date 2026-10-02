<?php

/**
 * API Router
 * Supports static routes and one-level dynamic segments: /resource/{id}
 * Batavia Madrigal Singers — Backend API
 */

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/MemberController.php';
require_once __DIR__ . '/../controllers/EventController.php';
require_once __DIR__ . '/../controllers/GalleryController.php';
require_once __DIR__ . '/../helpers/response.php';

$method = $_SERVER['REQUEST_METHOD'];

// Normalise URI — strip query string and leading/trailing slashes
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$uri = '/' . trim($uri, '/');

// ------------------------------------------------------------------
// Static routes  (method => [class, action])
// ------------------------------------------------------------------
$static = [
    // Auth
    'POST /api/v1/auth/register' => [AuthController::class,  'register'],
    'POST /api/v1/auth/login'    => [AuthController::class,  'login'],
    'POST /api/v1/auth/google'   => [AuthController::class,  'google'],
    'GET /api/v1/auth/me'        => [AuthController::class,  'me'],

    // Collections
    'GET /api/v1/members'        => [MemberController::class, 'index'],
    'POST /api/v1/members'       => [MemberController::class, 'store'],
    'GET /api/v1/events'         => [EventController::class,  'index'],
    'POST /api/v1/events'        => [EventController::class,  'store'],
    'GET /api/v1/gallery'        => [GalleryController::class,'index'],
    'POST /api/v1/gallery'       => [GalleryController::class,'store'],
];

$key = "$method $uri";

if (isset($static[$key])) {
    [$class, $action] = $static[$key];
    (new $class())->$action();
    exit; // respondSuccess/respondError already call exit, but be explicit
}

// ------------------------------------------------------------------
// Dynamic routes  /api/v1/{resource}/{id}
// ------------------------------------------------------------------
$dynamic = [
    // Members
    'GET /api/v1/members/{id}'    => [MemberController::class, 'show'],
    'PUT /api/v1/members/{id}'    => [MemberController::class, 'update'],
    'DELETE /api/v1/members/{id}' => [MemberController::class, 'destroy'],

    // Events
    'GET /api/v1/events/{id}'     => [EventController::class,  'show'],
    'PUT /api/v1/events/{id}'     => [EventController::class,  'update'],
    'DELETE /api/v1/events/{id}'  => [EventController::class,  'destroy'],

    // Gallery
    'GET /api/v1/gallery/{id}'    => [GalleryController::class,'show'],
    'PUT /api/v1/gallery/{id}'    => [GalleryController::class,'update'],
    'DELETE /api/v1/gallery/{id}' => [GalleryController::class,'destroy'],
];

foreach ($dynamic as $pattern => [$class, $action]) {
    // Build a regex from the pattern: replace {id} with a capture group
    $regex = '@^' . preg_replace('/\{[^}]+\}/', '(\d+)', $pattern) . '$@';
    [$patternMethod, $patternPath] = explode(' ', $pattern, 2);

    if ($patternMethod === $method && preg_match($regex, "$method $uri", $m)) {
        $id = (int) $m[1];
        (new $class())->$action($id);
        exit;
    }
}

respondError("Route not found: $method $uri", 404);
