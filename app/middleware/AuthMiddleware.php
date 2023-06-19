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
            $BearerToken = $_SERVER['HTTP_AUTHORIZATION'];
            $token = explode(' ', $BearerToken);

            try {
                $decodedPayload = $this->jwt->verifyAccessToken($token[1]);
                $_SESSION['user_id'] = $decodedPayload['user_id'];

                if (!$this->isUserLoggedIn($token[1])) {
                    $this->sendUnauthorizedResponse();
                }
            } catch (Exception $e) {
                $this->sendUnauthorizedResponse();
            }
        }
    }

    private function checkIfAuthenticationIsRequired()
    {
        $authRequiredRoutes = [];
        $productsRoutes = include BASE_PATH . '/app/routes/product.php';
        $productTypeRoutes = include BASE_PATH . '/app/routes/productType.php';
        $productTaxRoutes = include BASE_PATH . '/app/routes/productTax.php';
        $saleRoutes = include BASE_PATH . '/app/routes/sale.php';
        $authRequiredRoutes = array_merge($authRequiredRoutes,$productsRoutes,$productTypeRoutes,$productTaxRoutes,$saleRoutes);
        return $authRequiredRoutes;
    }

    function isUserLoggedIn($token)
    {
        try {
            $decodedPayload = $this->jwt->verifyAccessToken($token);

            $userId = $decodedPayload['user_id'];

            $db = (new Database())->connect();

            $stmt = $db->prepare("SELECT * FROM Token WHERE user_id = ? AND token = ?");
            $stmt->execute([$userId, $token]);
            $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($tokenData) {
                $expiresAt = strtotime($tokenData['expires_at']);
                $currentTimestamp = time();

                if ($expiresAt < $currentTimestamp) {
                    echo $expiresAt;
                    return false;
                }
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    private function sendUnauthorizedResponse()
    {
        http_response_code(401);
        exit('Unauthorized access');
    }
}
