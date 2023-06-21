<?php

function register($params)
{

    if(!$params['email'] || !$params['password'] || !$params['name']){
        http_response_code(422);
        exit(json_encode(['message' => 'The given data was invalid.',
        'error' => ['The email field is required.',
        'The password field is required.',
        'The name field is required.'
        ]
    ]));
    };

    include_once './database/Database.php';

    $hashedPassword = password_hash($params['password'], PASSWORD_DEFAULT);
    $database = new Database();
    $db = $database->connect();

    try {
        $sql = "INSERT INTO Users (name, password, email) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$params['name'], $hashedPassword, $params['email']]);
        exit(json_encode(['message' => 'user created successfully!']));
    } catch (PDOException $e) {
        http_response_code(400);
        exit(json_encode(['message' => 'Could not create user!', 'error' => $e->getMessage()]));
    }
}
