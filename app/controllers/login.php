<?php

function login($params)
{
    include_once './database/Database.php';
    include_once './app/Jwt.php';

    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');

    $database = new Database();
    $db = $database->connect();

    try {
        $email = $params['email'];
        $password = $params['password'];
        $userId = $params['id'];

        // Verificar se o usuário existe no banco de dados
        $stmt = $db->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Usuário não encontrado
            http_response_code(401);
            echo json_encode(['message' => 'User not found']);
            return;
        }

        // Verificar a senha do usuário
        if (password_verify($password, $user['password'])) {

            try{
                $jwtClass = new Jwt();
                $accessToken = $jwtClass->generateAccessToken($user);
            }catch(PDOException $e){
                http_response_code(500);
                echo json_encode(['message' => 'Server error']);
            }
        
            unset($user['password']);
            unset($user['created_at']);
            unset($user['updated_at']);
            echo json_encode(['user' => $user, 'bearer_token' => "Bearer $accessToken"]);
        } else {
  
            http_response_code(401);
            echo json_encode(['message' => 'Incorrect access data.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Server error']);
    }

}
