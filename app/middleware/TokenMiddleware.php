<?php
class TokenMiddleware
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function handleRequest()
    {
        try {
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $bearerToken = $_SERVER['HTTP_AUTHORIZATION'];

                $token = explode(' ', $bearerToken)[1];

                $userData = $this->getUserDataFromToken($token);

                if ($userData) {
                    header('Content-Type: application/json');
                    unset($userData['password']);
                    return json_encode($userData);
                    exit;
                }
            }

            http_response_code(401);
            exit(json_encode(['message' => 'Unauthorized access.']));
        } catch (Exception $e) {
            http_response_code(500);
            exit(json_encode(['message' => 'Internal Server Error.', 'error' => $e->getMessage()]));
        }
    }

    private function getUserDataFromToken($token)
    {
        $stmt = $this->db->prepare("SELECT Users.* FROM Users INNER JOIN Token ON Users.id = Token.user_id WHERE Token.token = ?");
        $stmt->execute([$token]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userData;
    }
}