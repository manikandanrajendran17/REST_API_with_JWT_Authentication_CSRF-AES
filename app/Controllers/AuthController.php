<?php

require_once "../app/models/User.php";
require_once "../app/helpers/JWT.php";
require_once "../app/helpers/Response.php";
require_once "../app/middleware/CsrfMiddleware.php";

class AuthController
{
    private $userModel;

    public function __construct()

    { $this->userModel = new User();}

    // Register

    public function register($request)
    {
        $body = $request['body'];   
        $name = trim($body['name'] ?? '');
        $email = trim($body['email'] ?? '');
        $password = trim($body['password'] ?? '');

        if (!$name || !$email || !$password) 
            {
            Response::json([ "message" => "All fields required"],400);
        }

        $existingUser = $this->userModel->findByEmail($email);

        if ($existingUser) { Response::json(
                [  "message" => "Email exists"],409);
        }

        $hashedPassword = password_hash($password,PASSWORD_DEFAULT);

        $this->userModel->create([
            "name" => $name,
            "email" => $email,
            "password" => $hashedPassword
        ]);

        Response::json(["message" => "Register success"],201);
    }



    // Login

    public function login($request)
    {
        $body = $request['body'];
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';

        $user = $this->userModel->findByEmail($email);

        if (!$user) {Response::json(["message" => "User not found"],404);}
        $valid = password_verify($password,$user['password']);

        if (!$valid) {
            Response::json( ["message" => "Wrong password"],401);
        }
        $payload = ["user_id" => $user['id'],
        "email" => $user['email'],
        "iat" => time(),
        "exp" => time() + $_ENV['JWT_EXPIRY']
        ];

        $accessToken = JWT::generate($payload);

        session_start();
        $csrfToken = CsrfMiddleware::generateToken();

        $refreshToken = bin2hex(random_bytes(40) );
        $expiry = date("Y-m-d H:i:s",time() + 172800);
        $this->userModel->saveRefreshToken( $user['id'], $refreshToken, $expiry);

        setcookie("refresh_token",$refreshToken,time() + 172800,"/","",false,true);
        Response::json(["status" => "success","message" => "Login Successful",
        "data" => ["access_token" => $accessToken, "csrf_token" => $csrfToken, "expires_in" => $_ENV['JWT_EXPIRY'],
        "user" => ["id" => $user['id'],"name" => $user['name'],"email" => $user['email']]
    ]
]);
    }

    // Refresh Token

    public function refresh()
    {
        $refreshToken = $_COOKIE['refresh_token']?? '';

        $user =  $this->userModel->findRefreshToken( $refreshToken );

        if (!$user) { Response::json(["message" => "Login again"],401);
        }

        $payload = [ "user_id" => $user['id'],"email" => $user['email'],"iat" => time(), "exp" => time() + 120];
        $newAccessToken = JWT::generate($payload);
        Response::json(["status" => "success","message" => "Login Successful","refresh_token" => $newAccessToken ]
        );
    }

    public function logout()
   {

     $refreshToken = $_COOKIE['refresh_token'] ?? '';
     $user = $this->userModel->findRefreshToken($refreshToken);

     if($user)
           {$this->userModel->clearRefreshToken($user['id']);}

            setcookie("refresh_token","",time()-3600,"/");
            Response::json(["message"=>"Logout success"]);
            }
}