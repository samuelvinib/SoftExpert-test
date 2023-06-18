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
                $this->sendUnauthorizedResponse();
            }

            $token = $_SERVER['HTTP_AUTHORIZATION'];

            try {

                $decodedPayload = $this->jwt->verifyAccessToken($token);

                $_SESSION['user_id'] = $decodedPayload['user_id'];
            } catch (Exception $e) {
                $this->sendUnauthorizedResponse();
            }
        }
    }

    private function checkIfAuthenticationIsRequired()
    {

        $authRequiredRoutes = [];
        $userRoutes = include BASE_PATH . './app/routes/users.php';
        $authRequiredRoutes = array_merge($authRequiredRoutes);
        return $authRequiredRoutes;

    }

    private function sendUnauthorizedResponse()
    {
        http_response_code(401);
        exit('Unauthorized access');
    }
}
