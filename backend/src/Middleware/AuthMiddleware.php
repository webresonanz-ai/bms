<?php

namespace BMS\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class AuthMiddleware
{
    public function handle(callable $next, ...$params): mixed
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $this->jsonResponse(['error' => 'Authorization header missing'], 401);
            return null;
        }

        $token = $matches[1];
        $secret = $_ENV['JWT_SECRET'] ?? getenv('JWT_SECRET') ?? 'your-secret-key';
        
        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            $GLOBALS['auth_user'] = $decoded;
            return $next(...$params);
        } catch (ExpiredException $e) {
            $this->jsonResponse(['error' => 'Token expired'], 401);
        } catch (SignatureInvalidException $e) {
            $this->jsonResponse(['error' => 'Invalid token signature'], 401);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => 'Invalid token'], 401);
        }
        
        return null;
    }

    public function jsonResponse(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}