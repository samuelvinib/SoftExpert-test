<?php

function register($params){
include_once './database/Database.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

$data = json_decode(file_get_contents("php://input"));
    $database = new Database();
    $db = $database->connect();

    $sql = "INSERT INTO Users (name, password, email, role) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$name, $password, $email, $role]);

    // Verifique se o usuário foi criado com sucesso
    if ($stmt->rowCount() > 0) {
        return true; // Sucesso
    } else {
        return false; // Falha
    }

}
