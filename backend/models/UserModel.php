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
            'SELECT id, name, email, password_hash, role, google_id, avatar_url, created_at
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
            'SELECT id, name, email, role, google_id, avatar_url, created_at
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
     * Google-only accounts have a NULL password_hash → always false.
     */
    public function verifyPassword(string $plain, ?string $hash): bool
    {
        if ($hash === null || $hash === '') {
            return false;
        }
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

    // ------------------------------------------------------------------
    // Google OAuth
    // ------------------------------------------------------------------

    /**
     * Find a user by their Google `sub` ID.
     */
    public function findByGoogleId(string $googleId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, email, password_hash, role, google_id, avatar_url, created_at
             FROM users
             WHERE google_id = :gid
             LIMIT 1'
        );
        $stmt->execute([':gid' => $googleId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * Insert a new user that signed up via Google (no password).
     * Works with or without the new google columns (backwards compatible).
     */
    public function createGoogleUser(string $name, string $email, string $googleId, ?string $avatarUrl = null): int
    {
        $email = strtolower(trim($email));

        if ($this->hasColumn('google_id')) {
            $stmt = $this->db->prepare(
                'INSERT INTO users (name, email, password_hash, role, google_id, avatar_url, created_at)
                 VALUES (:name, :email, NULL, :role, :gid, :avatar, NOW())'
            );
            $stmt->execute([
                ':name'   => trim($name) !== '' ? trim($name) : $email,
                ':email'  => $email,
                ':role'   => 'member',
                ':gid'    => $googleId,
                ':avatar' => $avatarUrl,
            ]);
        } else {
            // Fallback for DBs where migration 003 hasn't run yet:
            // store a random unusable password hash instead of NULL.
            $random = password_hash(bin2hex(random_bytes(32)), PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
            $stmt = $this->db->prepare(
                'INSERT INTO users (name, email, password_hash, role, created_at)
                 VALUES (:name, :email, :hash, :role, NOW())'
            );
            $stmt->execute([
                ':name'  => trim($name) !== '' ? trim($name) : $email,
                ':email' => $email,
                ':hash'  => $random,
                ':role'  => 'member',
            ]);
        }

        return (int) $this->db->lastInsertId();
    }

    /**
     * Link a Google ID (+ avatar) to an existing email account.
     * Used when a password user later signs in with the same Google email.
     */
    public function linkGoogleId(int $userId, string $googleId, ?string $avatarUrl = null): void
    {
        if (!$this->hasColumn('google_id')) {
            return;
        }
        $stmt = $this->db->prepare(
            'UPDATE users SET google_id = :gid, avatar_url = COALESCE(:avatar, avatar_url) WHERE id = :id'
        );
        $stmt->execute([':gid' => $googleId, ':avatar' => $avatarUrl, ':id' => $userId]);
    }

    /**
     * Check whether a column exists on the users table (migration-safe).
     */
    private function hasColumn(string $column): bool
    {
        try {
            $stmt = $this->db->prepare('SHOW COLUMNS FROM users LIKE :col');
            $stmt->execute([':col' => $column]);
            return (bool) $stmt->fetch();
        } catch (Throwable) {
            return false;
        }
    }
}
