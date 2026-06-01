<?php

require_once '../app/helpers/Response.php';

class CsrfMiddleware
{
    public static function generateToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
             session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] =bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verify()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $headers = getallheaders();
        $token = $headers['X-CSRF-Token'] ?? '';

        if (
            empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            Response::json(["message" => "Invalid CSRF Token"], 403);
        }
    }
}