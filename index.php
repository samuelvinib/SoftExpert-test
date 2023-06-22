<?php

define('BASE_PATH', __DIR__);


$requestUri = $_SERVER['REQUEST_URI'];

$basePathLength = strlen(dirname($_SERVER['SCRIPT_NAME']));
$routePath = substr($requestUri, $basePathLength);

$routePath = strtok($routePath, '?');

$routes = [];


$routes = [
    '' => 'app/routes/webAppRoute.php',
    'api/register' => 'app/routes/userRoute.php',
    'uploads' => 'app/routes/imageRoute.php',
    'api/login' => 'app/routes/userRoute.php',
    'api/product' => 'app/routes/productRoute.php',
    'api/product_info' => 'app/routes/productRoute.php',
    'api/product_tax' => 'app/routes/productTaxRoute.php',
    'api/product_type' => 'app/routes/productTypeRoute.php',
    'api/sale' => 'app/routes/saleRoute.php',
];

if ($routePath === false) {

    $matchedRoute = 'app/routes/webAppRoute.php';
    header('Content-Type: text/html');
} elseif (array_key_exists($routePath, $routes)) {

    $matchedRoute = $routes[$routePath];
    header('Content-Type: application/json');
} else {
    header('Content-Type: application/json');
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
    exit;
}

include BASE_PATH . '/' . $matchedRoute;