<?php

/**
 * Google Identity Services — ID token verification.
 * Batavia Madrigal Singers — Backend API
 *
 * Verifies a Google ID token (JWT) by calling Google's tokeninfo endpoint
 * and checking audience / expiry / email verification.
 * No Composer dependency required.
 */

/**
 * Verify a Google ID token and return its payload.
 *
 * @param  string $idToken  The `credential` string sent by GIS on the frontend.
 * @return array|null       Decoded payload (sub, email, name, picture, ...) or null if invalid.
 */
function verifyGoogleIdToken(string $idToken): ?array
{
    $idToken = trim($idToken);
    if ($idToken === '') {
        return null;
    }

    $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);

    $ctx = stream_context_create([
        'http' => [
            'method'  => 'GET',
            'timeout' => 8,
            'header'  => "Accept: application/json\r\n",
        ],
        'ssl' => [
            'verify_peer'      => true,
            'verify_peer_name' => true,
        ],
    ]);

    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false) {
        return null;
    }

    $payload = json_decode($raw, true);
    if (!is_array($payload)) {
        return null;
    }

    // tokeninfo returns `error_description` on failure
    if (isset($payload['error']) || isset($payload['error_description'])) {
        return null;
    }

    // ---- Audience check: token must be issued for OUR Client ID ----
    $expectedAud = defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : '';
    if ($expectedAud === '' || ($payload['aud'] ?? '') !== $expectedAud) {
        return null;
    }

    // ---- Expiry check ----
    if (isset($payload['exp']) && (int) $payload['exp'] < time()) {
        return null;
    }

    // ---- Issuer check ----
    $iss = $payload['iss'] ?? '';
    if ($iss !== 'https://accounts.google.com' && $iss !== 'accounts.google.com') {
        return null;
    }

    // ---- Email must be present + verified ----
    if (empty($payload['email']) || ($payload['email_verified'] ?? 'false') !== 'true') {
        return null;
    }

    return $payload;
}
