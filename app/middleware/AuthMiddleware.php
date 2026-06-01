<?php

require_once '../app/helpers/JWT.php';
require_once '../app/helpers/Response.php';

class AuthMiddleware
{
    public static function handle()
    {
        $headers = getallheaders();
        $header = $headers['Authorization'] ?? '';

        if (!$header) {
            Response::json(
                ['status' => false,'message' => 'Token missing',], 401
            );
        }

        $token = trim(str_replace('Bearer ', '', $header));
        $user = JWT::verify($token);

        if (!$user) {
            Response::json(
                ['status' => false,'message' => 'Invalid Token',],401
            );
        }

        return $user;
    }
}