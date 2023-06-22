<?php
require_once './app/controllers/ProductController.php';

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

$productController = new ProductController();

if ($method === 'GET' && $uri_request === 'product') {
    $requestBody = $_GET;
    $data = $requestBody;
    if($data['id']){
        $result = $productController->getProductById($data['id']);
        return json_encode($result);
    }
    $result = $productController->getAllProducts();
    return json_encode($result);
}else if ($method === 'GET' && $uri_request === 'product_info') {
    $requestBody = $_GET;
    $data = $requestBody;
    $result = $productController->getAllProductInfo();
    return json_encode($result);
}elseif ($method === 'POST' && $uri_request === 'product') {
    $roleMiddleware->handleRequest();
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    $result = $productController->createProduct();
    return $result;
}elseif ($method === 'PUT' && $uri_request === 'product') {
    $roleMiddleware->handleRequest();
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    $result = $productController->updateProduct(($data['id']),$data['name'],$data['price'],$data['type_product_id']);
    return $result;
}elseif ($method === 'DELETE' && $uri_request === 'product') {
    $roleMiddleware->handleRequest();
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    $result = $productController->deleteProduct(($data['id']));
    return $result;
} else {
    http_response_code(405);
    echo json_encode(['message' => 'method not allowed']);
}
