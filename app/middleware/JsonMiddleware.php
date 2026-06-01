<?php

require_once '../app/helpers/Response.php';

class JsonMiddleware
{
    public static function handle()
    {
        header('Content-Type: application/json');

        $method = $_SERVER['REQUEST_METHOD'];

        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

            if (strpos($contentType, 'application/json') === false) {
                Response::json(
                    [
                        'status' => false,
                        'message' => 'JSON required',
                    ],
                    400
                );
            }

            $body = json_decode(
                file_get_contents('php://input'),
                true
            );

            if (json_last_error() !== JSON_ERROR_NONE) {
                Response::json(
                    [
                        'status' => false,
                        'message' => 'Invalid JSON',
                    ],
                    400
                );
            }

            return [
                'body' => $body,
            ];
        }

        return [];
    }
}