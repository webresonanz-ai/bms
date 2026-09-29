<?php

/**
 * AuthController — handles /api/v1/auth/* endpoints.
 * Batavia Madrigal Singers — Backend API
 */

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validate.php';
require_once __DIR__ . '/../helpers/jwt.php';
require_once __DIR__ . '/../config/app.php';

class AuthController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // ------------------------------------------------------------------
    // POST /api/v1/auth/register
    // ------------------------------------------------------------------
    public function register(): never
    {
        $input = $this->getJsonInput();

        $errors = validateRegistration($input);
        if (!empty($errors)) {
            respondError('Validation failed.', 422, $errors);
        }

        if ($this->userModel->emailExists($input['email'])) {
            respondError('Validation failed.', 422, [
                'email' => 'This email address is already registered.'
            ]);
        }

        $userId = $this->userModel->create(
            $input['name'],
            $input['email'],
            $input['password']
        );

        $user  = $this->userModel->findById($userId);
        $token = $this->buildToken($user);

        respondSuccess([
            'token' => $token,
            'user'  => $this->safeUser($user),
        ], 201);
    }

    // ------------------------------------------------------------------
    // POST /api/v1/auth/login
    // ------------------------------------------------------------------
    public function login(): never
    {
        $input = $this->getJsonInput();

        $errors = validateLogin($input);
        if (!empty($errors)) {
            respondError('Validation failed.', 422, $errors);
        }

        $user = $this->userModel->findByEmail($input['email']);

        // Use a generic message to avoid user enumeration
        if (!$user || !$this->userModel->verifyPassword($input['password'], $user['password_hash'])) {
            respondError('Invalid email or password.', 401);
        }

        $token = $this->buildToken($user);

        respondSuccess([
            'token' => $token,
            'user'  => $this->safeUser($user),
        ]);
    }

    // ------------------------------------------------------------------
    // GET /api/v1/auth/me   (requires Authorization: Bearer <token>)
    // ------------------------------------------------------------------
    public function me(): never
    {
        $payload = $this->requireAuth();

        $user = $this->userModel->findById($payload['sub']);
        if (!$user) {
            respondError('User not found.', 404);
        }

        respondSuccess(['user' => $this->safeUser($user)]);
    }

    // ------------------------------------------------------------------
    // Private helpers
    // ------------------------------------------------------------------

    /** Parse and return the JSON request body. */
    private function getJsonInput(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (!is_array($data)) {
            respondError('Request body must be valid JSON.', 400);
        }

        return $data;
    }

    /** Build a signed JWT for the given user row. */
    private function buildToken(array $user): string
    {
        return jwtEncode([
            'sub'  => $user['id'],
            'name' => $user['name'],
            'role' => $user['role'],
            'iat'  => time(),
            'exp'  => time() + JWT_EXPIRY_SECONDS,
        ]);
    }

    /**
     * Validate the Bearer token from the Authorization header.
     *
     * @return array  Decoded JWT payload.
     */
    private function requireAuth(): array
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!str_starts_with($header, 'Bearer ')) {
            respondError('Authentication required.', 401);
        }

        $token   = substr($header, 7);
        $payload = jwtDecode($token);

        if ($payload === null) {
            respondError('Invalid or expired token.', 401);
        }

        return $payload;
    }

    /** Return a user array stripped of sensitive fields. */
    private function safeUser(array $user): array
    {
        unset($user['password_hash']);
        return $user;
    }
}
