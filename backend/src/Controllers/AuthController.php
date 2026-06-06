<?php

namespace BMS\Controllers;

use BMS\Models\User;
use Firebase\JWT\JWT;

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['email'], $data['password'])) {
            $this->jsonResponse(['error' => 'Email and password required'], 400);
            return;
        }

        $user = $this->userModel->findByEmail($data['email']);
        
        if (!$user || !password_verify($data['password'], $user['password'])) {
            $this->jsonResponse(['error' => 'Invalid credentials'], 401);
            return;
        }

        $payload = [
            'iss' => 'bms-api',
            'aud' => 'bms-frontend',
            'iat' => time(),
            'exp' => time() + 86400,
            'user_id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        $token = JWT::encode($payload, $_ENV['JWT_SECRET'] ?? 'your-secret-key', 'HS256');
        
        $this->jsonResponse([
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'avatar' => $user['avatar'],
                'phone' => $user['phone'],
                'location' => $user['location'],
                'specialization' => $user['specialization'],
                'bio' => $user['bio'],
                'joinDate' => $user['join_date'],
                'achievements' => json_decode($user['achievements'], true) ?? []
            ],
            'token' => $token
        ]);
    }

    public function register(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name'], $data['email'], $data['password'])) {
            $this->jsonResponse(['error' => 'Name, email and password required'], 400);
            return;
        }

        $existingUser = $this->userModel->findByEmail($data['email']);
        if ($existingUser) {
            $this->jsonResponse(['error' => 'Email already registered'], 409);
            return;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $id = $this->userModel->create($data);
        $user = $this->userModel->find($id);

        $payload = [
            'iss' => 'bms-api',
            'aud' => 'bms-frontend',
            'iat' => time(),
            'exp' => time() + 86400,
            'user_id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        $token = JWT::encode($payload, $_ENV['JWT_SECRET'] ?? 'your-secret-key', 'HS256');

        $this->jsonResponse([
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'avatar' => $user['avatar'],
                'phone' => $user['phone'],
                'location' => $user['location'],
                'specialization' => $user['specialization'],
                'bio' => $user['bio'],
                'joinDate' => $user['join_date'],
                'achievements' => json_decode($user['achievements'], true) ?? []
            ],
            'token' => $token
        ], 201);
    }

    public function profile(): void
    {
        $user = $GLOBALS['auth_user'] ?? null;
        
        if (!$user) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        $userData = $this->userModel->find((int) $user->user_id);
        if (!$userData) {
            $this->jsonResponse(['error' => 'User not found'], 404);
            return;
        }

        $this->jsonResponse([
            'id' => $userData['id'],
            'name' => $userData['name'],
            'email' => $userData['email'],
            'role' => $userData['role'],
            'avatar' => $userData['avatar'],
            'phone' => $userData['phone'],
            'location' => $userData['location'],
            'specialization' => $userData['specialization'],
            'bio' => $userData['bio'],
            'joinDate' => $userData['join_date'],
            'achievements' => json_decode($userData['achievements'], true) ?? []
        ]);
    }

    public function updateProfile(): void
    {
        $user = $GLOBALS['auth_user'] ?? null;
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$user) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        $updateData = array_filter($data, fn($key) => in_array($key, ['name', 'phone', 'location', 'specialization', 'bio']), ARRAY_FILTER_USE_KEY);
        
        $this->userModel->update((int) $user->user_id, $updateData);
        $this->jsonResponse(['message' => 'Profile updated']);
    }

    private function jsonResponse(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}