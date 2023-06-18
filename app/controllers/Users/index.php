<?php
require_once './app/controllers/Users/register.php';
require_once './app/controllers/Users/login.php';

// require_once './app/middleware/RoleAdminMiddleware.php';
// require_once './app/middleware/TokenMiddleware.php';

// $middleware = new TokenMiddleware();

$method = $_SERVER['REQUEST_METHOD'];
$uri_request = explode('/',$_SERVER['REQUEST_URI']);

// $Adminmiddleware = new RoleAdminMiddleware();
// $isPermitted = $Adminmiddleware->handleRequest();
// echo json_encode($isPermitted);


if ($method === 'POST' && $uri_request[2] === 'register') {
    $requestBody = file_get_contents('php://input');
    $requestData = json_decode($requestBody, true);
    if (isset($requestData['name'], $requestData['password'], $requestData['email'])) {
        register($requestData);
    } else {
        http_response_code(400);
        echo 'Missing data in request body';
    }
}elseif ($method === 'POST' && $uri_request[2] === 'login') {
    $requestBody = file_get_contents('php://input');
    $requestData = json_decode($requestBody, true);
    if (isset($requestData['password'], $requestData['email'])) {
        echo $middleware->handleRequest();
        login($requestData);
    } else {
        http_response_code(400);
        echo 'Missing data in request body';
    }
} else {
    http_response_code(405);
    echo 'method not allowed';
}
