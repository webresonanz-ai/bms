<?php

/**
 * Front controller — single entry point for all API requests.
 * Batavia Madrigal Singers — Backend API
 *
 * All requests should be routed here via .htaccess (Apache) or
 * the equivalent Nginx rewrite rule.
 */

declare(strict_types=1);

// Always respond with JSON
header('Content-Type: application/json; charset=utf-8');

// Handle CORS first (also exits on pre-flight OPTIONS)
require_once __DIR__ . '/middleware/cors.php';

// Route the request
require_once __DIR__ . '/routes/api.php';
