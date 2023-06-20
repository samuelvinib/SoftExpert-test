<?php

function updateEnvKey($envFilePath, $keyName, $newValue) {

    $envFile = file_get_contents($envFilePath);

    $oldValue = '';

    if (strpos($envFile, $keyName . '=') !== false) {

        $matches = [];
        preg_match('/' . $keyName . '=(.*)/', $envFile, $matches);
        $oldValue = $matches[1];

        if ($oldValue !== '') {

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
        echo 'Warning: The key ' . $keyName . ' not found in .env file';
    }
}
$envFilePath = '.env';
$keyName = 'APP_KEY';
$newValue = 'sua_nova_app_key';

updateEnvKey($envFilePath, $keyName, $newValue);
