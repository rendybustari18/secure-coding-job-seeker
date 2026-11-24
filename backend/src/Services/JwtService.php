<?php
require_once __DIR__ . '/../../config/env.php';

class JwtService
{
    private static string $secret = JWT_SECRET;

    public static function encode(array $payload): string
    {
        $header  = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode($payload);

        $base64Header  = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        $signature       = hash_hmac('sha256', $base64Header . '.' . $base64Payload, self::$secret, true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    }
    public static function decode(string $jwt): array|false
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return false;
        }

        $payload = json_decode(base64_decode($parts[1]), true);
        return $payload;
    }
    public static function getSecret(): string
    {
        return self::$secret;
    }
}

