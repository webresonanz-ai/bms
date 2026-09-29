<?php

/**
 * Database Configuration
 * Credentials are read from the .env file via the env() helper.
 * Batavia Madrigal Singers — Backend API
 */

/**
 * Create and return a singleton PDO database connection.
 *
 * @throws RuntimeException when the connection fails.
 */
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $host    = env('DB_HOST', 'localhost');
    $port    = (int) env('DB_PORT', 3306);
    $name    = env('DB_NAME', 'batavia_madrigal');
    $user    = env('DB_USER', 'root');
    $pass    = env('DB_PASS', '');
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=$charset";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection error.']);
        exit;
    }

    return $pdo;
}
