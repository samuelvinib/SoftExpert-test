<?php

function login($params)
{

    include_once './database/Database.php';
    include_once './app/controllers/Jwt.php';


    if(!$params['email'] || !$params['password']){
        http_response_code(422);
        return (json_encode(['message' => 'The given data was invalid.',
        'error' => ['The email field is required.',
        'The password field is required.'
        ]
    ]));
    };

    $database = new Database();
    $db = $database->connect();

    try {
        $email = $params['email'];
        $password = $params['password'];

        $stmt = $db->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {

            http_response_code(401);
            exit(json_encode(['message' => 'User not found']));
        }

        if (password_verify($password, $user['password'])) {

            try{
                $jwtClass = new Jwt();
                $accessToken = $jwtClass->generateAccessToken($user);
            }catch(PDOException $e){
                http_response_code(500);
                exit(json_encode(['message' => 'Server error', 'error' => $e->getMessage()]));
            }
        
            unset($user['password']);
            unset($user['created_at']);
            unset($user['updated_at']);
            echo (json_encode(['user' => $user, 'bearer_token' => "Bearer $accessToken"]));
        } else {
  
            http_response_code(401);
            exit(json_encode(['message' => 'Incorrect access data.']));
        }
    } catch (PDOException $e) {
        http_response_code(500);
        exit(json_encode(['message' => 'Server error', 'error' => $e->getMessage()]));
    }

}
