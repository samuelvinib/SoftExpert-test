<?php

$method = $_SERVER['REQUEST_METHOD'];
$uri_request = explode('?', explode('/', $_SERVER['REQUEST_URI'])[1])[0];
    

    $buildPath = __DIR__ . '\build';

    $indexContent = file_get_contents($buildPath . '\index.html');
    
    $indexContent = str_replace('<head>', '<head>' . PHP_EOL . '<script src="/build/static/js/main.e24e5c41.js"></script>' . PHP_EOL . '<script src="/build/static/js/mmain.e24e5c41.js.map"></script>' . PHP_EOL . '<script src="/build/static/js/2.chunk.js"></script>', $indexContent);

    echo $indexContent;
    exit();
