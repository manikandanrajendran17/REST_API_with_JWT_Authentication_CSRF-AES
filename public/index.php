<?php

require_once '../config/config.php';

require_once '../app/core/Router.php';

require_once '../app/middleware/JsonMiddleware.php';
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/middleware/CsrfMiddleware.php';

require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/PatientController.php';
require_once '../app/helpers/AES.php';

// Receive JSON Request
$request = JsonMiddleware::handle();

// Get URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove Project Path
$basePath = '/project/PHP_Task/php_Task_09_22/public';

$uri = str_replace($basePath, '', $uri);

// Get Request Method
$method = $_SERVER['REQUEST_METHOD'];

// Send Request To Router
Router::handle($uri, $method, $request);