<?php

/**
 * JSON response helpers.
 * Batavia Madrigal Singers — Backend API
 */

/**
 * Send a JSON success response and exit.
 *
 * @param mixed $data    Any JSON-serialisable payload.
 * @param int   $status  HTTP status code (default 200).
 */
function respondSuccess(mixed $data = null, int $status = 200): never
{
    http_response_code($status);
    $body = ['success' => true];
    if ($data !== null) {
        $body['data'] = $data;
    }
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Send a JSON error response and exit.
 *
 * @param string $message  Human-readable error message.
 * @param int    $status   HTTP status code (default 400).
 * @param array  $errors   Optional field-level validation errors.
 */
function respondError(string $message, int $status = 400, array $errors = []): never
{
    http_response_code($status);
    $body = ['success' => false, 'message' => $message];
    if (!empty($errors)) {
        $body['errors'] = $errors;
    }
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
