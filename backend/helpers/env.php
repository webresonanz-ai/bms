<?php

/**
 * Minimal .env file loader.
 * Parses KEY=VALUE pairs and populates $_ENV and putenv().
 * Supports:
 *   - Inline comments  (#)
 *   - Quoted values    ("value" or 'value')
 *   - Empty values     (KEY=)
 *   - Export prefix    (export KEY=VALUE)
 *
 * Batavia Madrigal Singers — Backend API
 */

function loadEnv(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        throw new RuntimeException(".env file not found or not readable at: $path");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // Skip comments and blank lines
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        // Strip optional "export " prefix
        if (str_starts_with($line, 'export ')) {
            $line = substr($line, 7);
        }

        // Must contain an = sign
        if (!str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);

        // Strip inline comment (only outside quotes)
        $value = stripInlineComment($value);

        // Strip surrounding quotes
        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        // Don't overwrite values already set in the real environment
        if (!array_key_exists($key, $_ENV) && getenv($key) === false) {
            putenv("$key=$value");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }
}

/**
 * Remove a trailing inline comment from an unquoted value.
 * e.g.  "localhost  # the db host"  →  "localhost"
 */
function stripInlineComment(string $value): string
{
    // If the value is quoted, leave it alone
    if (
        (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
        (str_starts_with($value, "'") && str_ends_with($value, "'"))
    ) {
        return $value;
    }

    $pos = strpos($value, ' #');
    if ($pos !== false) {
        $value = substr($value, 0, $pos);
    }

    return trim($value);
}

/**
 * Retrieve an environment variable with an optional default.
 *
 * @param  string      $key
 * @param  mixed       $default
 * @return string|mixed
 */
function env(string $key, mixed $default = null): mixed
{
    // Check $_ENV first, then getenv() as fallback
    if (array_key_exists($key, $_ENV)) {
        $value = $_ENV[$key];
    } else {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
    }

    // Cast common boolean/null strings
    return match (strtolower((string) $value)) {
        'true',  '(true)'  => true,
        'false', '(false)' => false,
        'null',  '(null)'  => null,
        default            => $value,
    };
}
