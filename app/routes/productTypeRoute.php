<?php
require_once './app/controllers/ProductTypeController.php';

require_once './app/middleware/RoleAdminMiddleware.php';
require_once './app/middleware/TokenMiddleware.php';
require_once './app/middleware/AuthMiddleware.php';

$authMiddleware = new AuthMiddleware();
$authMiddleware->handleRequest();

$method = $_SERVER['REQUEST_METHOD'];
$uri_request = explode('?',explode('/',$_SERVER['REQUEST_URI'])[2])[0];

$tokenMiddleware = new TokenMiddleware();
$userData = $tokenMiddleware->handleRequest();

$roleMiddleware = new RoleAdminMiddleware();

$productController = new ProductTypeController();

if ($method === 'GET' && $uri_request === 'product_type') {
    $requestBody = $_GET;
    $data = $requestBody;
    if($data['id']){
        $result = $productController->getProductTypeById($data['id']);
        return json_encode($result);
    }
    $result = $productController->getAllProductTypes();
    return json_encode($result);
}elseif ($method === 'POST' && $uri_request === 'product_type') {
    $roleMiddleware->handleRequest();
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    $result = $productController->createProductType($data['name']);
    return $result;
}elseif ($method === 'PUT' && $uri_request === 'product_type') {
    $roleMiddleware->handleRequest();
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    $result = $productController->updateProductType(($data['id']),$data['name']);
    return $result;
}elseif ($method === 'DELETE' && $uri_request === 'product_type') {
    $roleMiddleware->handleRequest();
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    $result = $productController->deleteProductType(($data['id']));
    return $result;
} else {
    http_response_code(405);
    echo json_encode(['message' => 'method not allowed']);
}
