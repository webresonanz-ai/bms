<?php

/**
 * Application-level configuration.
 * All values are sourced from the .env file via the env() helper.
 * Batavia Madrigal Singers — Backend API
 */

define('ALLOWED_ORIGIN',      env('ALLOWED_ORIGIN',      'http://localhost:5173'));
define('JWT_SECRET',           env('JWT_SECRET',           'fallback-secret-change-me'));
define('JWT_EXPIRY_SECONDS',  (int) env('JWT_EXPIRY_SECONDS', 3600));
define('BCRYPT_COST',         (int) env('BCRYPT_COST',        12));
