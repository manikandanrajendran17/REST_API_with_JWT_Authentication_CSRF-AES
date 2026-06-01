<?php

require_once '../app/core/Database.php';

class User
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Find User By Email
    public function findByEmail($email)
    {
        $stmt = $this->conn->prepare(
            'SELECT *
            FROM users
            WHERE email = ?'
        );

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Create User
    public function create($data)
    {
        $stmt = $this->conn->prepare(

            'INSERT INTO users
            (   name,email, password)
            VALUES
            ( ?, ?, ? )'
        );

        $stmt->bind_param( 'sss', $data['name'], $data['email'], $data['password'] );
        return $stmt->execute();
    }

    // Save Refresh Token
    public function saveRefreshToken($userId, $token, $expiry)
    {
        $stmt = $this->conn->prepare('UPDATE users SET
                refresh_token = ?,
                refresh_token_expiry = ?
            WHERE id = ?'
        );

        $stmt->bind_param('ssi',$token,$expiry,$userId);

        return $stmt->execute();
    }

    // Find Refresh Token
    public function findRefreshToken($token)
    {
        $stmt = $this->conn->prepare(
            'SELECT *
            FROM users
            WHERE refresh_token = ?
            AND refresh_token_expiry > NOW()'
        );

        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function clearRefreshToken($userId)
    {
        $stmt = $this->conn->prepare(
            'UPDATE users
            SET
                refresh_token = NULL,
                refresh_token_expiry = NULL
            WHERE id = ?'
        );

        $stmt->bind_param('i', $userId);

        return $stmt->execute();
    }
}