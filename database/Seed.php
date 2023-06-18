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
            $this->seedSale();
            $this->seedSaleItem();

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

    private function seedSale()
    {
        $sales = [
            ['date' => '2023-01-01', 'total_value' => 50.99, 'total_tax' => 5.0, 'user_id' => 1],
            ['date' => '2023-01-02', 'total_value' => 100.50, 'total_tax' => 8.5, 'user_id' => 2],
            ['date' => '2023-01-03', 'total_value' => 25.75, 'total_tax' => 3.2, 'user_id' => 3],
        ];

        foreach ($sales as $sale) {
            $date = $sale['date'];
            $total_value = $sale['total_value'];
            $total_tax = $sale['total_tax'];
            $user_id = $sale['user_id'];

            // Verifica se a venda já existe
            $stmt = $this->db->prepare("SELECT id FROM Sale WHERE date = ? AND user_id = ?");
            $stmt->execute([$date, $user_id]);
            $existingSale = $stmt->fetch();

            if (!$existingSale) {
                $stmt = $this->db->prepare("INSERT INTO Sale (date, total_value, total_tax, user_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$date, $total_value, $total_tax, $user_id]);
            }
        }
    }

    private function seedSaleItem()
    {
        $saleItems = [
            ['sale_id' => 1, 'product_id' => 1, 'quantity' => 2, 'item_total_value' => 21.98, 'tax_amount' => 2.2],
            ['sale_id' => 1, 'product_id' => 2, 'quantity' => 1, 'item_total_value' => 19.99, 'tax_amount' => 1.7],
            ['sale_id' => 2, 'product_id' => 2, 'quantity' => 3, 'item_total_value' => 59.97, 'tax_amount' => 5.1],
        ];

        foreach ($saleItems as $saleItem) {
            $sale_id = $saleItem['sale_id'];
            $product_id = $saleItem['product_id'];
            $quantity = $saleItem['quantity'];
            $item_total_value = $saleItem['item_total_value'];
            $tax_amount = $saleItem['tax_amount'];

            // Verifica se o item de venda já existe
            $stmt = $this->db->prepare("SELECT id FROM SaleItem WHERE sale_id = ? AND product_id = ?");
            $stmt->execute([$sale_id, $product_id]);
            $existingSaleItem = $stmt->fetch();

            if (!$existingSaleItem) {
                $stmt = $this->db->prepare("INSERT INTO SaleItem (sale_id, product_id, quantity, item_total_value, tax_amount) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$sale_id, $product_id, $quantity, $item_total_value, $tax_amount]);
            }
        }
    }
}

$seed = new Seed();
$seed->run();
