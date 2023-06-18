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
            $token = explode(' ',$BearerToken);

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
        $userRoutes = include BASE_PATH . '/app/routes/users.php';
        $authRequiredRoutes = array_merge($authRequiredRoutes);
        return $authRequiredRoutes;
    }

    function isUserLoggedIn($token) {
        try {
            $decodedPayload = $this->jwt->verifyAccessToken($token);
            
            // Extrair o ID do usuário do payload
            $userId = $decodedPayload['user_id'];
            
            // Conectar ao banco de dados
            $db = (new Database())->connect();
    
            // Verificar se o token existe no banco de dados
            $stmt = $db->prepare("SELECT * FROM Token WHERE user_id = ? AND token = ?");
            $stmt->execute([$userId, $token]);
            $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
            echo $tokenData;
            if ($tokenData) {
                // Token válido e encontrado no banco de dados, o usuário está logado
                return true;
            } else {
                // Token inválido ou não encontrado no banco de dados, o usuário não está logado
                return false;
            }
        } catch (Exception $e) {
            // Ocorreu um erro na verificação do token
            return false;
        }
    }

    private function sendUnauthorizedResponse()
    {
        http_response_code(401);
        exit('Unauthorized access');
        
    }
}