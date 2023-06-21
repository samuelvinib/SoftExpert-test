<?php
require_once './app/controllers/ProductTaxController.php';

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

$productTaxController = new ProductTaxController();

if ($method === 'GET' && $uri_request === 'product_tax') {
    $requestBody = $_GET;
    $data = $requestBody;
    if($data['id']){
        $result = $productTaxController->getProductTaxById($data['id']);
        return json_encode($result);
    }
    $result = $productTaxController->getAllProductTaxes();
    return json_encode($result);
}elseif ($method === 'POST' && $uri_request === 'product_tax') {
    $roleMiddleware->handleRequest();
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    $result = $productTaxController->createProductTax($data['type_product_id'], $data['tax_percentage']);
    return $result;
}elseif ($method === 'PUT' && $uri_request === 'product_tax') {
    $roleMiddleware->handleRequest();
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    $result = $productTaxController->updateProductTax(($data['id']),$data['type_product_id'],$data['tax_percentage']);
    return $result;
}elseif ($method === 'DELETE' && $uri_request === 'product_tax') {
    $roleMiddleware->handleRequest();
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    $result = $productTaxController->deleteProductTax(($data['id']));
    return  $result;
} else {
    http_response_code(405);
    echo json_encode(['message' => 'method not allowed']);
}
