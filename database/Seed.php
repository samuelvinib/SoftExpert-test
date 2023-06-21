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
            ['name' => 'amaciante', 'price' => 10.99, 'type_product_id' => 1, 'image_path' => 'uploads/amaciante_downy_concentrado_brisa_de_verao_1_5l_10015_1_d0d2b0b60248c45f81bd211c403b7130.webp'],
            ['name' => 'sabonete', 'price' => 2.99, 'type_product_id' => 2 , 'image_path' => 'uploads/71U2Kfhht4L._AC_UF1000,1000_QL80_.jpg'],
            ['name' => 'detergente', 'price' => 5.99, 'type_product_id' => 1 , 'image_path' => 'uploads/detergente_liquido_clear_ype_500ml_4773_1_d08d46a82a496cb0053b5c424727c41c.webp'],
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
            ['name' => 'John Test', 'password' => 'test123', 'email' => 'test@teste.com', 'role' => 'user'],
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
