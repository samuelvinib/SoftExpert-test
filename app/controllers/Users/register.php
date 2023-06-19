<?php

function register($params){
include_once './database/Database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

$data = json_decode(file_get_contents("php://input"));
    $hashedPassword = password_hash($params['password'], PASSWORD_DEFAULT);
    $database = new Database();
    $db = $database->connect();

    try{
        $sql = "INSERT INTO Users (name, password, email) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$params['name'], $hashedPassword, $params['email']]);
    echo 'user created successfully!';
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode($e);
    }

}
