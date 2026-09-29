<?php

/**
 * UserModel — database operations for the `users` table.
 * Batavia Madrigal Singers — Backend API
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    // ------------------------------------------------------------------
    // Queries
    // ------------------------------------------------------------------

    /**
     * Find a user by their email address.
     *
     * @return array|null  User row without the password_hash field, or null.
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, email, password_hash, role, created_at
             FROM users
             WHERE email = :email
             LIMIT 1'
        );
        $stmt->execute([':email' => strtolower(trim($email))]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * Find a user by their primary key ID.
     *
     * @return array|null  User row without the password_hash field, or null.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, email, role, created_at
             FROM users
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * Insert a new user record.
     *
     * @param  string $name      Full name.
     * @param  string $email     Email address (stored lowercase).
     * @param  string $password  Plain-text password (will be hashed).
     * @param  string $role      'member' (default) or 'admin'.
     * @return int               The new user's ID.
     */
    public function create(
        string $name,
        string $email,
        string $password,
        string $role = 'member'
    ): int {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);

        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password_hash, role, created_at)
             VALUES (:name, :email, :password_hash, :role, NOW())'
        );
        $stmt->execute([
            ':name'          => trim($name),
            ':email'         => strtolower(trim($email)),
            ':password_hash' => $hash,
            ':role'          => $role,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Verify a plain-text password against a stored hash.
     */
    public function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    /**
     * Check whether an email address is already registered.
     */
    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute([':email' => strtolower(trim($email))]);

        return (bool) $stmt->fetchColumn();
    }
}
