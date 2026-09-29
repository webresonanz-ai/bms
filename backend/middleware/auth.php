<?php

/**
 * Auth middleware helpers — reusable JWT guard functions.
 * Batavia Madrigal Singers — Backend API
 */

require_once __DIR__ . '/../helpers/jwt.php';
require_once __DIR__ . '/../helpers/response.php';

/**
 * Require a valid Bearer token. Returns the decoded payload.
 */
function requireAuth(): array
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

    if (!str_starts_with($header, 'Bearer ')) {
        respondError('Authentication required.', 401);
    }

    $payload = jwtDecode(substr($header, 7));

    if ($payload === null) {
        respondError('Invalid or expired token.', 401);
    }

    return $payload;
}

/**
 * Require the authenticated user to have the 'admin' role.
 * Returns the decoded payload on success.
 */
function requireAdmin(): array
{
    $payload = requireAuth();

    if (($payload['role'] ?? '') !== 'admin') {
        respondError('Admin access required.', 403);
    }

    return $payload;
}
