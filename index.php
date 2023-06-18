<?php

define('BASE_PATH', __DIR__);

require_once './app/middleware/AuthMiddleware.php';

// Criar uma instância do middleware
$middleware = new AuthMiddleware();

// Chamar o método handleRequest() para lidar com a solicitação
$middleware->handleRequest();


$requestUri = $_SERVER['REQUEST_URI'];

$basePathLength = strlen(dirname($_SERVER['SCRIPT_NAME']));
$routePath = substr($requestUri, $basePathLength);

$routePath = strtok($routePath, '?');

$routes = [];

$usersRoutes = include BASE_PATH . '/app/routes/users.php';
$routes = array_merge($routes, $usersRoutes);

if (array_key_exists($routePath, $routes)) {
    include BASE_PATH . '/' . $routes[$routePath];
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}