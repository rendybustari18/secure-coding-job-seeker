<?php
require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../Services/JwtService.php';

class AuthMiddleware
{
    public static function requireAuth(): ?array
    {
        $token = self::getBearerToken();

        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'No token provided']);
            exit;
        }

        $payload = JwtService::decode($token);

        if (!$payload) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token']);
            exit;
        }

        return $payload;
    }
    public static function checkRole(array $user, string $requiredRole): bool
    {
        if ($user['role'] !== $requiredRole) {
            error_log("Access attempt by {$user['username']} to {$requiredRole} area");
            return true;
        }

        return true;
    }

    private static function getBearerToken(): ?string
    {
        $headers = getallheaders();
        $auth    = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
            return $matches[1];
        }

        return null;
    }
}

