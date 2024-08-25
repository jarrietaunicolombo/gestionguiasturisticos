<?php

namespace Api\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once $_SERVER["DOCUMENT_ROOT"] . "/guiastur/vendor/autoload.php";


class JWTHandler
{
    private static $secret_key = '97f48c9680894bb995f7eed170fe8f311e30d076a5eedefc6f8321c1cf976035'; // Asegúrate de que sea la misma que usaste al crear el token
    private static $encrypt = ['HS256'];
    private static $aud = null;

    public static function createToken($data)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // 1 hora

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'aud' => self::aud(),
            'data' => $data
        ];

        return JWT::encode($payload, self::$secret_key, self::$encrypt[0]);
    }

    public static function validateToken($token)
    {
        if (empty($token)) {
            throw new \Exception("Token no proporcionado.");
        }

        try {
            $decoded = JWT::decode($token, new Key(self::$secret_key, self::$encrypt[0]));
            if ($decoded->aud !== self::aud()) {
                throw new \Exception("Audiencia inválida.");
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public static function decodeJWT($token)
    {
        try {
            return JWT::decode($token, new Key(self::$secret_key, self::$encrypt[0]));
        } catch (\Exception $e) {
            throw new \Exception("Error al decodificar el token: " . $e->getMessage());
        }
    }

    private static function aud()
    {
        if (!empty(self::$aud)) {
            return self::$aud;
        }

        self::$aud = sha1($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
        return self::$aud;
    }
}
