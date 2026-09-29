<?php

/**
 * CORS middleware.
 * Sets the necessary headers and handles pre-flight OPTIONS requests.
 * Batavia Madrigal Singers — Backend API
 */

require_once __DIR__ . '/../config/app.php';

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Only allow the configured frontend origin
if ($origin === ALLOWED_ORIGIN) {
    header('Access-Control-Allow-Origin: ' . ALLOWED_ORIGIN);
} else {
    // Allow requests with no Origin header (e.g., curl, Postman in tests)
    if ($origin !== '') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Origin not allowed.']);
        exit;
    }
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400'); // Cache pre-flight for 24 h

// Respond to pre-flight and stop
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
