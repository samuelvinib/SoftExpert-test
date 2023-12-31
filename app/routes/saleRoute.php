<?php
require_once './app/controllers/SaleController.php';

require_once './app/middleware/RoleAdminMiddleware.php';
require_once './app/middleware/TokenMiddleware.php';
require_once './app/middleware/AuthMiddleware.php';

$authMiddleware = new AuthMiddleware();
$authMiddleware->handleRequest();

$method = $_SERVER['REQUEST_METHOD'];
$uri_request = explode('?', explode('/', $_SERVER['REQUEST_URI'])[2])[0];

$tokenMiddleware = new TokenMiddleware();
$userData = json_decode($tokenMiddleware->handleRequest(), true);

$roleMiddleware = new RoleAdminMiddleware();

$saleController = new SaleController();

if ($method === 'GET' && $uri_request === 'sale') {
    $roleMiddleware->handleRequest();
    $requestBody = $_GET;
    $data = $requestBody;
    if ($data['id']) {
        $result = $saleController->getSaleById($data['id']);
        echo json_encode($result);
        exit;
    }
    $result = $saleController->getAllSales();
    echo json_encode($result);
} else if ($method === 'GET' && $uri_request === 'cart') {
    $requestBody = $_GET;
    $data = $requestBody;
    $result = $saleController->getAllCarts($userData['id']);
    echo json_encode($result);
    exit;
}else if ($method === 'PUT' && $uri_request === 'checkout') {
    $result = $saleController->checkout($userData['id']);
    echo json_encode($result);
    exit;
} else if ($method === 'GET' && $uri_request === 'open_cart') {
    $requestBody = $_GET;
    $data = $requestBody;
    $result = $saleController->getLastCart($userData['id']);
    echo json_encode($result);
    exit;
}  elseif ($method === 'POST' && $uri_request === 'add_cart') {
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    $result = $saleController->createSale($data['id'], $data['quantity'] ,$data['price'], $data['tax']['percentage'] , $userData['id']);
    echo $result;
} else {
    http_response_code(405);
    echo json_encode(['message' => 'method not allowed']);
}
