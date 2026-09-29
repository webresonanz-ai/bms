<?php

/**
 * Application-level configuration constants.
 * Batavia Madrigal Singers — Backend API
 */

// Allowed origin for CORS (set to your frontend dev server or production domain)
define('ALLOWED_ORIGIN', 'http://localhost:5173');

// JWT / session
define('JWT_SECRET', 'CHANGE_THIS_TO_A_RANDOM_SECRET_STRING_IN_PRODUCTION');
define('JWT_EXPIRY_SECONDS', 3600); // 1 hour

// Password hashing cost factor
define('BCRYPT_COST', 12);

// API version prefix
define('API_VERSION', 'v1');
