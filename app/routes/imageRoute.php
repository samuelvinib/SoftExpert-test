<?php


$method = $_SERVER['REQUEST_METHOD'];
$uri_request = explode('?',explode('/',$_SERVER['REQUEST_URI'])[2])[0];

exit($uri_request);

if ($method === 'GET' && $uri_request === 'uploads') {
    $imageName = $_GET['image'];

    $imageFolder = './uploads/';

    $imagePath = $imageFolder . $imageName;
    if (file_exists($imagePath)) {
        $imageMimeType = mime_content_type($imagePath);

        header('Content-Type: ' . $imageMimeType);

        readfile($imagePath);
        exit('kk');
    }
}

http_response_code(404);
exit(json_encode(['message' => 'Imagem não encontrada']));
