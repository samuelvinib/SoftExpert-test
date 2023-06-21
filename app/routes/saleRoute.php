<?php
require_once './app/controllers/SaleController.php';

require_once './app/middleware/RoleAdminMiddleware.php';
require_once './app/middleware/TokenMiddleware.php';
require_once './app/middleware/AuthMiddleware.php';

$authMiddleware = new AuthMiddleware();
$authMiddleware->handleRequest();

$method = $_SERVER['REQUEST_METHOD'];
$uri_request = explode('?',explode('/',$_SERVER['REQUEST_URI'])[2])[0];

$tokenMiddleware = new TokenMiddleware();
$userData = json_decode($tokenMiddleware->handleRequest(), true);

$roleMiddleware = new RoleAdminMiddleware();

$saleController = new SaleController();

if ($method === 'GET' && $uri_request === 'sale') {
    $roleMiddleware->handleRequest();
    $requestBody = $_GET;
    $data = $requestBody;
    if($data['id']){
        $result = $saleController->getSaleById($data['id']);
        echo json_encode($result);
        exit;
    }
    $result = $saleController->getAllSales();
    echo json_encode($result);
}elseif ($method === 'POST' && $uri_request === 'sale') {
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    echo $userData['id'];
    // $result = $saleController->createSale($data['date']);
    echo $result;
} else {
    http_response_code(405);
    echo json_encode(['message' => 'method not allowed']);
}
