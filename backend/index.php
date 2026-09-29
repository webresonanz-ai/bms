<?php

/**
 * Front controller — single entry point for all API requests.
 * Batavia Madrigal Singers — Backend API
 */

declare(strict_types=1);

// 1. Load environment variables first — everything else depends on them
require_once __DIR__ . '/helpers/env.php';
loadEnv(__DIR__ . '/.env');

// 2. Always respond with JSON
header('Content-Type: application/json; charset=utf-8');

// 3. Handle CORS (also exits on OPTIONS pre-flight)
require_once __DIR__ . '/middleware/cors.php';

// 4. Route the request
require_once __DIR__ . '/routes/api.php';
