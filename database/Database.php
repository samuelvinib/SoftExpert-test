<?php
error_reporting(0);
require_once './env.php';
class Database
{
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    private $connection;
  
    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'];
        $this->port = $_ENV['DB_PORT'];
        $this->db_name = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];
    }

    public function connect()
    {
        $this->connection = null;

        try {

            $this->connection = new PDO('pgsql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            exit(json_encode(['message' => 'Connection Error', 'error' => $e->getMessage()]));
        }

        return $this->connection;
    }
}
