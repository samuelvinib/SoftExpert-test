<?php
error_reporting(0);
require_once './env.php';
class Migrations
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

  // DB Connect
  public function connect()
  {
    $this->connection = null;

    try {
      // create a new connection with PostgreSQL database;
      $this->connection = new PDO('pgsql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->db_name, $this->username, $this->password);
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $sqlProductType = "CREATE TABLE ProductType (
        id SERIAL PRIMARY KEY,
        name VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );";
    
    $sqlProduct = "CREATE TABLE Product (
        id SERIAL PRIMARY KEY,
        name VARCHAR(255),
        price DECIMAL(10, 2),
        tipo_product_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (tipo_product_id) REFERENCES ProductType(id)
    );";
    
    $sqlProductTax = "CREATE TABLE ProductTax (
        id SERIAL PRIMARY KEY,
        tipo_product_id INT,
        tax_percentage DECIMAL(5, 2),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (tipo_product_id) REFERENCES ProductType(id)
    );";
    
    $sqlUsers = "CREATE TABLE Users (
        id SERIAL PRIMARY KEY,
        name VARCHAR(255),
        password VARCHAR(255),
        email VARCHAR(255),
        role VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );";

    $sqlToken = "CREATE TABLE Token (
      id SERIAL PRIMARY KEY,
      user_id INT,
      token VARCHAR(255),
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      expires_at TIMESTAMP,
      FOREIGN KEY (user_id) REFERENCES Users(id)
    );";

    $sqlRefreshToken = "CREATE TABLE RefreshToken (
      id SERIAL PRIMARY KEY,
      user_id INT,
      refresh_token VARCHAR(255),
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      expires_at TIMESTAMP,
      FOREIGN KEY (user_id) REFERENCES Users(id)
    );";
    
    $sqlSale = "CREATE TABLE Sale (
        id SERIAL PRIMARY KEY,
        date DATE,
        total_value DECIMAL(10, 2),
        total_tax DECIMAL(10, 2),
        user_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES Users(id)
    );";
    
    $sqlSaleItem = "CREATE TABLE SaleItem (
        id SERIAL PRIMARY KEY,
        sale_id INT,
        product_id INT,
        quantity INT,
        item_total_value DECIMAL(10, 2),
        tax_amount DECIMAL(10, 2),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sale_id) REFERENCES Sale(id),
        FOREIGN KEY (product_id) REFERENCES Product(id)
    );";

      $this->connection->exec($sqlProductType);
      $this->connection->exec($sqlProduct);
      $this->connection->exec($sqlProductTax);
      $this->connection->exec($sqlUsers);
      $this->connection->exec($sqlToken);
      $this->connection->exec($sqlRefreshToken);
      $this->connection->exec($sqlSale);
      $this->connection->exec($sqlSaleItem);

      echo "Database and tables successfully created!";
    } catch (PDOException $e) {
      echo 'Connection Error: ' . $e->getMessage();
    }
  }
}

$createDB = new Migrations();
$createDB->connect();
