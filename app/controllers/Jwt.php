<?php
include_once './database/Database.php';
class Jwt {
    private $secretKey;
    private $db;

    public function __construct() {
        $this->secretKey = $_ENV['APP_KEY'];
    }
    
    public function generateAccessToken($user) {
        $expirationTime = time() + 3600;
    
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
    
        $payload = [
            'user_id' => $user['id'],
            'exp' => $expirationTime
        ];
    
        $encodedHeader = $this->base64UrlEncode(json_encode($header));
        $encodedPayload = $this->base64UrlEncode(json_encode($payload));
    
        $signature = hash_hmac('sha256', $encodedHeader . '.' . $encodedPayload, $this->secretKey, true);
    
        $encodedSignature = $this->base64UrlEncode($signature);
    
        $token = $encodedHeader . '.' . $encodedPayload . '.' . $encodedSignature;
    
        $database = new Database();
        $db = $database->connect();
    
        $expiresAt = date('Y-m-d H:i:s', $expirationTime);
    
        $sql = "INSERT INTO Token (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$user['id'], $token, $expiresAt]);
    
        return $token;
    }
    
    public function verifyAccessToken($token) {
        $parts = explode('.', $token);
        $encodedHeader = $parts[0];
        $encodedPayload = $parts[1];
        $encodedSignature = $parts[2];
        
        if (count($parts) !== 3) {
            http_response_code(401);
            exit(json_encode(['message' => 'Invalid token']));
        }
        
        $signature = $this->base64UrlDecode($encodedSignature);
        $expectedSignature = hash_hmac('sha256', $encodedHeader . '.' . $encodedPayload, $this->secretKey, true);
        
        if (!hash_equals($signature, $expectedSignature)) {
            http_response_code(401);
            exit(json_encode(['message' => 'Invalid token']));
        }
        
        $decodedHeader = json_decode($this->base64UrlDecode($encodedHeader), true);
        $decodedPayload = json_decode($this->base64UrlDecode($encodedPayload), true);
        
        $currentTime = time();
        if ($decodedPayload['exp'] < $currentTime) {
            http_response_code(401);
            exit(json_encode(['message' => 'Expired token']));
        }
        
        return $decodedPayload;
    }
    
    private function base64UrlEncode($data) {
        $base64 = base64_encode($data);
        $base64Url = strtr($base64, '+/', '-_');
        return rtrim($base64Url, '=');
    }
    
    private function base64UrlDecode($data) {
        $base64Url = strtr($data, '-_', '+/');
        $base64 = base64_decode($base64Url);
        return $base64;
    }
    
}
