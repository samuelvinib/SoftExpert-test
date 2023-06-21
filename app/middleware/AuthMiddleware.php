<?php
require_once './app/controllers/Jwt.php';
class AuthMiddleware
{
    private $jwt;

    public function __construct()
    {
        $this->jwt = new Jwt();
    }

    public function handleRequest()
    {
        $requiresAuth = $this->checkIfAuthenticationIsRequired();
        if ($requiresAuth) {
            if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $this->sendUnauthorizedResponse('Unauthorized access');
            }
            $BearerToken = $_SERVER['HTTP_AUTHORIZATION'];
            $token = explode(' ', $BearerToken);

            try {
                $decodedPayload = $this->jwt->verifyAccessToken($token[1]);
                $_SESSION['user_id'] = $decodedPayload['user_id'];

                if (!$decodedPayload) {
                    $this->sendUnauthorizedResponse('Exception: Invalid token');
                }
            } catch (Exception $e) {
                $this->sendUnauthorizedResponse($e);
            }
        }
    }

    private function checkIfAuthenticationIsRequired()
    {
        $routes = [
            'api/product' => 'app/routes/productRoute.php',
            'api/product_tax' => 'app/routes/productTaxRoute.php',
            'api/product_type' => 'app/routes/productTypeRoute.php',
            'api/sale' => 'app/routes/saleRoute.php',
        ];
        return $routes;
    }

    private function sendUnauthorizedResponse($alert)
    {
        http_response_code(401);
        exit(json_encode($alert));
    }
}
