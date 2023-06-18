<?php

define('BASE_PATH', __DIR__);

require_once './app/middleware/AuthMiddleware.php';

$middleware = new AuthMiddleware();
$middleware->handleRequest();


$requestUri = $_SERVER['REQUEST_URI'];

$basePathLength = strlen(dirname($_SERVER['SCRIPT_NAME']));
$routePath = substr($requestUri, $basePathLength);

$routePath = strtok($routePath, '?');

$routes = [];

$usersRoutes = include BASE_PATH . '/app/routes/users.php';
$productsRoutes = include BASE_PATH . '/app/routes/product.php';
$productTypeRoutes = include BASE_PATH . '/app/routes/productType.php';
$productTaxRoutes = include BASE_PATH . '/app/routes/productTax.php';
$routes = array_merge($routes, $usersRoutes,$productsRoutes,$productTypeRoutes, $productTaxRoutes);

if (array_key_exists($routePath, $routes)) {
    include BASE_PATH . '/' . $routes[$routePath];
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}