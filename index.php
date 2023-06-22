<?php

define('BASE_PATH', __DIR__);


$requestUri = $_SERVER['REQUEST_URI'];

$basePathLength = strlen(dirname($_SERVER['SCRIPT_NAME']));
$routePath = substr($requestUri, $basePathLength);

$routePath = strtok($routePath, '?');

$routes = [];


$routes = [
    false => '/react.php',
    'api/register' => 'app/routes/userRoute.php',
    'uploads' => 'app/routes/imageRoute.php',
    'api/login' => 'app/routes/userRoute.php',
    'api/product' => 'app/routes/productRoute.php',
    'api/product_info' => 'app/routes/productRoute.php',
    'api/product_tax' => 'app/routes/productTaxRoute.php',
    'api/product_type' => 'app/routes/productTypeRoute.php',
    'api/add_cart' => 'app/routes/saleRoute.php',
    'api/cart' => 'app/routes/saleRoute.php',
    'api/checkout' => 'app/routes/saleRoute.php',
    'api/open_cart' => 'app/routes/saleRoute.php',
];

if (array_key_exists($routePath, $routes)) {
    header('Content-Type: application/json');
    include BASE_PATH . '/' . $routes[$routePath];
} else {
    header('Content-Type: application/json');
    http_response_code(404);
    echo json_encode(['error' => 'Invalid route!']);
}