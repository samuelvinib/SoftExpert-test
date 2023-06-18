<?php

function updateEnvKey($envFilePath, $keyName, $newValue) {
    // Ler o conteúdo atual do arquivo .env
    $envFile = file_get_contents($envFilePath);

    $oldValue = '';

    if (strpos($envFile, $keyName . '=') !== false) {
        // A chave existe no arquivo .env
        $matches = [];
        preg_match('/' . $keyName . '=(.*)/', $envFile, $matches);
        $oldValue = $matches[1];

        if ($oldValue !== '') {
            // A chave já possui um valor
            echo 'Warning: The key already has a defined value: ' . $oldValue;
        } else {
                $length = 32;
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $apiKey = '';
                
                for ($i = 0; $i < $length; $i++) {
                    $randomIndex = random_int(0, strlen($characters) - 1);
                    $apiKey .= $characters[$randomIndex];
                }
                $envFile = file_get_contents('.env');
                $envFile = str_replace('APP_KEY=', "APP_KEY=$apiKey", $envFile);
                file_put_contents('.env', $envFile);
                echo 'api key successfully generated!';
        }
    } else {
        // A chave não existe no arquivo .env
        echo 'Warning: The key ' . $keyName . ' not found in .env file';
    }
}

// Exemplo de uso
$envFilePath = '.env';
$keyName = 'APP_KEY';
$newValue = 'sua_nova_app_key';

updateEnvKey($envFilePath, $keyName, $newValue);
