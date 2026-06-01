<?php

class Router
{
    public static function handle($uri,$method,$request)
    {

        // Register

        if ($uri == "/api/register" && $method == "POST") {

            $controller = new AuthController();
            $controller->register( $request);
        }


        // Login

        elseif ( $uri == "/api/login" && $method == "POST") {

            $controller = new AuthController();
            $controller->login( $request );
        }

        
        // Refresh Token Route

         elseif($uri =="/api/token/refresh" && $method == "POST")
         {
             $controller = new AuthController();
             $controller->refresh();
         }
         elseif($uri == "/api/logout" && $method == "POST")
            {
             CsrfMiddleware::verify();
             $controller = new AuthController();
             $controller->logout();
            }


        // Get Patients

        elseif (  $uri == "/api/patients" &&  $method == "GET"  ) 
            {
             AuthMiddleware::handle();
             $controller = new PatientController();
             $controller->index();
           }


        // Create Patient

        elseif (  $uri == "/api/patients" && $method == "POST" ) 
            {
             AuthMiddleware::handle();
             CsrfMiddleware::verify();
             $controller = new PatientController();
             $controller->store($request);
        }


        // Update Patient

        elseif (preg_match(  "#^/api/patients/([0-9]+)$#", $uri, $matches ) && $method == "PUT") 
            {

            AuthMiddleware::handle();
            CsrfMiddleware::verify();
            $id = $matches[1];
            $controller = new PatientController();
            $controller->update( $id, $request);
        }


        // Delete Patient

        elseif (
            preg_match(  "#^/api/patients/([0-9]+)$#",  $uri,  $matches )  &&  $method == "DELETE") 
            {

            AuthMiddleware::handle();
            CsrfMiddleware::verify();
            $id = $matches[1];
            $controller = new PatientController();
            $controller->delete( $id );
        }


        // Route Not Found

        else {

            http_response_code( 404 );

            echo json_encode([ "message" => "Route not found" ]);
        }
    }
}