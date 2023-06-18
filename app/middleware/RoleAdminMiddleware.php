<?php
require_once './app/controllers/Jwt.php';
class RoleAdminMiddleware
{
    public function handleRequest()
    {
        if ($this->isAdmin()) {
            return true;
        }

        http_response_code(403);
        exit('Access denied. You do not have permission to perform this action.');
    }

    private function isAdmin()
    {
        $db = (new Database())->connect();
        $BearerToken = $_SERVER['HTTP_AUTHORIZATION'];
        $token = explode(' ',$BearerToken);

        $stmt = $db->prepare("SELECT Users.* FROM Users INNER JOIN Token ON Users.id = Token.user_id WHERE Token.token = ?");
        $stmt->execute([$token[1]]);
        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (isset($tokenData['role']) && $tokenData['role'] === 'admin') {
            return true;
        }
        return false;
    }
}