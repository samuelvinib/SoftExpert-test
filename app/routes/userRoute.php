<?php
require_once './app/controllers/register.php';
require_once './app/controllers/login.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri_request = explode('/',$_SERVER['REQUEST_URI']);



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
        login($requestData);
    } else {
        http_response_code(400);
        echo 'Missing data in request body';
    }
} else {
    http_response_code(405);
    echo 'method not allowed';
}
