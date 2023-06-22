<?php

class ProductController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function createProduct()
    {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $type_product_id = $_POST['type_product_id'];
    
        if (!$name || !$price || !$type_product_id) {
            http_response_code(422);
            exit(json_encode([
                'message' => 'The given data was invalid.',
                'error' => [
                    'The name field is required.',
                    'The price field is required.',
                    'The type_product_id field is required.'
                ]
            ]));
        }
    
        if (!empty($_FILES['image']['name'])) {
            $imageName = md5(uniqid() . time()) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageTmpName = $_FILES['image']['tmp_name'];
            $imagePath = 'uploads/' . $imageName;
            move_uploaded_file($imageTmpName, $imagePath);
        } else {
            $imageName = ''; // Se nenhum arquivo de imagem foi enviado, defina o nome como vazio
        }
    
        try {
            $stmt = $this->db->prepare("INSERT INTO Product (name, price, type_product_id, image_path) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $price, $type_product_id, $imageName]);
    
            if ($stmt->rowCount() > 0) {
                http_response_code(201);
                echo (json_encode(['message' => 'New product created successfully.']));
            }
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(['message' => 'Failed to create a new product.', 'error' => $e->getMessage()]));
        }
    
        return false;
    }

    public function getAllProductInfo()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    p.id,
                    p.name,
                    p.price,
                    p.type_product_id,
                    p.image_path,
                    p.created_at,
                    p.updated_at,
                    pt.id AS type_id,
                    pt.name AS type_name,
                    pt.created_at AS type_created_at,
                    pt.updated_at AS type_updated_at,
                    ptax.id AS tax_id,
                    ptax.tax_percentage,
                    ptax.created_at AS tax_created_at,
                    ptax.updated_at AS tax_updated_at
                FROM
                    Product p
                LEFT JOIN
                    ProductType pt ON p.type_product_id = pt.id
                LEFT JOIN
                    ProductTax ptax ON pt.id = ptax.type_product_id
            ");
            $stmt->execute();
            $productsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            $products = [];
    
            foreach ($productsData as $productData) {
                $product = [
                    'id' => $productData['id'],
                    'name' => $productData['name'],
                    'price' => $productData['price'],
                    'image_path' => $productData['image_path'],
                    'type_product_id' => $productData['type_product_id'],
                    'created_at' => $productData['created_at'],
                    'updated_at' => $productData['updated_at'],
                    'type' => $productData['type_id'] ? [
                        'id' => $productData['type_id'],
                        'name' => $productData['type_name'],
                        'created_at' => $productData['type_created_at'],
                        'updated_at' => $productData['type_updated_at']
                    ] : null,
                    'tax' => $productData['tax_id'] ? [
                        'id' => $productData['tax_id'],
                        'percentage' => $productData['tax_percentage'],
                        'created_at' => $productData['tax_created_at'],
                        'updated_at' => $productData['tax_updated_at']
                    ] : null
                ];
    
                $products[] = $product;
            }
    
            echo json_encode($products);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["Error: " . $e->getMessage()]);
        }
    }
    
    

    public function getAllProducts()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Product");
            $stmt->execute();
            $product = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo(json_encode($product));
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(["Error: " => $e->getMessage()]));
        }
    }

    public function getProductById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Product WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            exit(json_encode($product));
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(["Error: " => $e->getMessage()]));
        }
    }

    public function updateProduct($id, $name, $price, $type_product_id)
    {

        if(!$name || !$price || !$type_product_id || !$id){
            http_response_code(422);
            exit(json_encode(['message' => 'The given data was invalid.',
            'error' => ['The name field is required.',
            'The price field is required.',
            'The type_product_id field is required.',
            'The id field is required.'
            ]
        ]));
        };

        try {
            $stmt = $this->db->prepare("UPDATE Product SET name = ?, price = ?, type_product_id = ? WHERE id = ?");
            $stmt->execute([$name, $price, $type_product_id, $id]);

            if ($stmt->rowCount() > 0) {
                exit(json_encode(["message: " => "Product updated successfully."]));
            }

            http_response_code(400);
            exit(json_encode(["message: " => "Product not found in database."]));
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(["Error: " => $e->getMessage()]));
        }

        return false;
    }


    public function deleteProduct($id)
    {

        if(!$id){
            http_response_code(422);
            exit(json_encode(['message' => 'The given data was invalid.',
            'error' => ['The id field is required.'
            ]
        ]));
        };

        try {
            $stmt = $this->db->prepare("DELETE FROM Product WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                exit(json_encode(["message: " => "Product deleted successfully."]));
            }
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(["message: " => "Error deleting product.", "error" => $e->getMessage()]));
        }

        http_response_code(400);
        exit(json_encode(["message: " => "Failed to delete product."]));
    }
}
