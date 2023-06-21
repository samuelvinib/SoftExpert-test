<?php
require_once './app/controllers/register.php';
require_once './app/controllers/login.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri_request = explode('/',$_SERVER['REQUEST_URI']);



if ($method === 'POST' && $uri_request[2] === 'register') {
    $requestBody = file_get_contents('php://input');
    $requestData = json_decode($requestBody, true);
    $result = register($requestData);
    echo $result;
}elseif ($method === 'POST' && $uri_request[2] === 'login') {
    $requestBody = file_get_contents('php://input');
    $requestData = json_decode($requestBody, true);
    $result = login($requestData);
    echo $result;
} else {
    http_response_code(405);
    echo json_encode(['message' => 'method not allowed']);
}
