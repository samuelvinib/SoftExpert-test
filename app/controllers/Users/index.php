<?php
require_once './app/controllers/Users/register.php';

$method = $_SERVER['REQUEST_METHOD'];


if ($method === 'POST') {
    $requestBody = file_get_contents('php://input');
    $requestData = json_decode($requestBody, true);

    if ($requestData) {
        $params = $requestData;
        $register = register($params);
    } else {
        http_response_code(400);
        echo 'Missing parameters in request body';
    }
} else {
    http_response_code(405);
    echo 'method not allowed';
}
