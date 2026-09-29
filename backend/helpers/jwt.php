<?php

/**
 * Minimal JWT implementation (HS256).
 * No external library required.
 * Batavia Madrigal Singers — Backend API
 */

require_once __DIR__ . '/../config/app.php';

/**
 * Generate a JWT token.
 *
 * @param array $payload  Data to encode (do NOT include sensitive info like passwords).
 * @return string
 */
function jwtEncode(array $payload): string
{
    $header  = base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload = base64UrlEncode(json_encode($payload));
    $sig     = base64UrlEncode(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));

    return "$header.$payload.$sig";
}

/**
 * Decode and verify a JWT token.
 *
 * @param string $token
 * @return array|null  Returns the payload array on success, null on failure.
 */
function jwtDecode(string $token): ?array
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return null;
    }

    [$header, $payload, $signature] = $parts;

    $expectedSig = base64UrlEncode(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));
    if (!hash_equals($expectedSig, $signature)) {
        return null; // Invalid signature
    }

    $data = json_decode(base64UrlDecode($payload), true);

    if (!is_array($data)) {
        return null;
    }

    // Check token expiry
    if (isset($data['exp']) && $data['exp'] < time()) {
        return null; // Expired
    }

    return $data;
}

/** @internal */
function base64UrlEncode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/** @internal */
function base64UrlDecode(string $data): string
{
    return base64_decode(strtr($data, '-_', '+/'));
}
