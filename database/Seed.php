<?php
require_once './database/Database.php';

class Seed
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function run()
    {
        try {
            $this->seedProductType();
            $this->seedProduct();
            $this->seedProductTax();
            $this->seedUsers();

            echo 'Seed completed successfully.';
        } catch (Exception $e) {
            echo 'Error seeding data: ' . $e->getMessage();
        }
    }

    private function seedProductType()
    {
        $productTypes = [
            ['name' => 'limpeza'],
            ['name' => 'higiene pessoal'],
            ['name' => 'bebida'],
        ];

        foreach ($productTypes as $productType) {
            $name = $productType['name'];

            // Verifica se o tipo de produto já existe
            $stmt = $this->db->prepare("SELECT id FROM ProductType WHERE name = ?");
            $stmt->execute([$name]);
            $existingType = $stmt->fetch();

            if (!$existingType) {
                $stmt = $this->db->prepare("INSERT INTO ProductType (name) VALUES (?)");
                $stmt->execute([$name]);
            }
        }
    }

    private function seedProduct()
    {
        $products = [
            ['name' => 'amaciante', 'price' => 10.99, 'type_product_id' => 1],
            ['name' => 'sabonete', 'price' => 2.99, 'type_product_id' => 2],
            ['name' => 'detergente', 'price' => 5.99, 'type_product_id' => 1],
        ];

        foreach ($products as $product) {
            $name = $product['name'];
            $price = $product['price'];
            $type_product_id = $product['type_product_id'];

            // Verifica se o produto já existe
            $stmt = $this->db->prepare("SELECT id FROM Product WHERE name = ?");
            $stmt->execute([$name]);
            $existingProduct = $stmt->fetch();

            if (!$existingProduct) {
                $stmt = $this->db->prepare("INSERT INTO Product (name, price, type_product_id) VALUES (?, ?, ?)");
                $stmt->execute([$name, $price, $type_product_id]);
            }
        }
    }

    private function seedProductTax()
    {
        $productTaxes = [
            ['type_product_id' => 1, 'tax_percentage' => 4.0],
            ['type_product_id' => 2, 'tax_percentage' => 2.5],
            ['type_product_id' => 3, 'tax_percentage' => 12.0],
        ];

        foreach ($productTaxes as $productTax) {
            $type_product_id = $productTax['type_product_id'];
            $tax_percentage = $productTax['tax_percentage'];

            // Verifica se o imposto para o tipo de produto já existe
            $stmt = $this->db->prepare("SELECT id FROM ProductTax WHERE type_product_id = ?");
            $stmt->execute([$type_product_id]);
            $existingTax = $stmt->fetch();

            if (!$existingTax) {
                $stmt = $this->db->prepare("INSERT INTO ProductTax (type_product_id, tax_percentage) VALUES (?, ?)");
                $stmt->execute([$type_product_id, $tax_percentage]);
            }
        }
    }

    private function seedUsers()
    {
        $users = [
            ['name' => 'John Doe', 'password' => 'adm1234', 'email' => 'adm@test.com','role' => 'admin'],
            ['name' => 'John Test', 'password' => 'password2', 'email' => 'test@teste.com', 'role' => 'user'],
            ['name' => 'User 3', 'password' => 'password3', 'email' => 'user3@example.com', 'role' => 'user'],
        ];

        foreach ($users as $user) {
            $name = $user['name'];
            $password = password_hash($user['password'], PASSWORD_DEFAULT);
            $email = $user['email'];
            $role = $user['role'];

            // Verifica se o usuário já existe
            $stmt = $this->db->prepare("SELECT id FROM Users WHERE email = ?");
            $stmt->execute([$email]);
            $existingUser = $stmt->fetch();

            if (!$existingUser) {
                $stmt = $this->db->prepare("INSERT INTO Users (name, password, email, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $password, $email, $role]);
            }
        }
    }
}

$seed = new Seed();
$seed->run();
