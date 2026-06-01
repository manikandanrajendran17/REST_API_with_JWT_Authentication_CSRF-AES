<?php

class AES
{
    public static function encrypt($data)
    {
        $cipher = "AES-256-CBC";
        $key = $_ENV['ENCRYPTION_KEY'];
        $iv = random_bytes( openssl_cipher_iv_length($cipher));
        $encrypted = openssl_encrypt( $data, $cipher, $key, 0, $iv);

        return base64_encode($iv . $encrypted);
    }

    public static function decrypt($encryptedData)
    {
        $cipher = "AES-256-CBC";
        $key = $_ENV['ENCRYPTION_KEY'];
        $data = base64_decode($encryptedData);
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);

        return openssl_decrypt($encrypted,$cipher,$key,0,$iv);
    }
}