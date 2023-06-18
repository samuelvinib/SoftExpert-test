<?php
require_once './app/controllers/ProductTax/ProductTaxController.php';

require_once './app/middleware/RoleAdminMiddleware.php';
require_once './app/middleware/TokenMiddleware.php';

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
        header('Content-Type: application/json');
        $result = $productTaxController->getProductTaxById($data['id']);
        echo json_encode($result);
        exit;
    }
    header('Content-Type: application/json');
    $result = $productTaxController->getAllProductTaxes();
    echo json_encode($result);
}elseif ($method === 'POST' && $uri_request === 'product_tax') {
    $roleMiddleware->handleRequest();
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    $result = $productTaxController->createProductTax($data['type_product_id'], $data['tax_percentage']);
    echo $result;
}elseif ($method === 'PUT' && $uri_request === 'product_tax') {
    $roleMiddleware->handleRequest();
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    echo $data['id'];
    $result = $productTaxController->updateProductTax(($data['id']),$data['type_product_id'],$data['tax_percentage']);
    echo $result;
}elseif ($method === 'DELETE' && $uri_request === 'product_tax') {
    $roleMiddleware->handleRequest();
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    $result = $productTaxController->deleteProductTax(($data['id']));
    echo $result;
} else {
    http_response_code(405);
    echo 'method not allowed';
}
