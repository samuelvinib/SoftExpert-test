<?php

$method = $_SERVER['REQUEST_METHOD'];
$uri_request = explode('?', explode('/', $_SERVER['REQUEST_URI'])[1])[0];
if ($method === 'GET' && $uri_request === '') {
    $buildPath = __DIR__ . '/build' ;
    $indexFilePath = $buildPath . '/index.html';
    $indexFilePathJs = $buildPath . '/static/js/main.66ea60ac.js';

    if (file_exists($indexFilePath)) {
        header('Content-Type: text/html');
        $indexContent = file_get_contents($indexFilePath);
        $indexContent = str_replace('src="/', 'src="/build/', $indexContent);
        $indexContent = str_replace('href="/', 'href="/build/', $indexContent);

        echo $indexContent;
        exit;
    } else {
        http_response_code(404);
        echo json_encode('Internal error!');
        exit;
    }
}