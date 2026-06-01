<?php

class JWT
{
    private static function base64UrlEncode($data)
    {
        return rtrim(
            strtr(base64_encode($data), '+/', '-_'),
            '='
        );
    }

    public static function generate($payload)
    {
        $secret = $_ENV['JWT_SECRET'];

        $header = json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT',
        ]);

        $header = self::base64UrlEncode($header);
        $payload = self::base64UrlEncode( json_encode($payload) );
        $signature = hash_hmac(
            'sha256',
            $header . '.' . $payload, $secret, true
        );

        $signature = self::base64UrlEncode($signature);

        return $header . '.' . $payload . '.' . $signature;
    }

    public static function verify($token)
    {
        $secret = $_ENV['JWT_SECRET'];

        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        $header = $parts[0];
        $payload = $parts[1];
        $providedSignature = $parts[2];

        $signature = hash_hmac('sha256',$header . '.' . $payload, $secret,true);
        $signature = self::base64UrlEncode($signature);

        if ($signature !== $providedSignature) {
            return false;
        }

        $payload = json_decode(
            base64_decode(strtr($payload, '-_', '+/')),
            true
        );

        if (isset($payload['exp']) && time() >= $payload['exp']) {
            return false;
        }

        return $payload;
    }
}