<?php

class ProductController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function createProduct($name, $price, $type_product_id)
    {

        if(!$name || !$price || !$type_product_id){
            http_response_code(422);
            exit(json_encode(['message' => 'The given data was invalid.',
            'error' => ['The name field is required.',
            'The price field is required.',
            'The type_product_id field is required.'
            ]
        ]));
        };

        try {
            $stmt = $this->db->prepare("INSERT INTO Product (name, price, type_product_id) VALUES (?, ?, ?)");
            $stmt->execute([$name, $price, $type_product_id]);

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

    public function getAllProducts()
    {
        try {
            $stmt = $this->db->prepare(" SELECT * FROM Product");
            $stmt->execute();
            $productsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $products = [];

            foreach ($productsData as $productData) {
                $stmt = $this->db->prepare(" SELECT * FROM ProductType WHERE id = ?");
                $stmt->execute([$productData['type_product_id']]);
                $productsTypeData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($productsTypeData as $productTypeData) {
                    $stmt = $this->db->prepare(" SELECT * FROM ProductTax WHERE id = ?");
                    $stmt->execute([$productTypeData['id']]);
                    $ProductsTax = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($ProductsTax as $ProductTax) {
                $product = [
                    'product' => [
                        'id' => $productData['id'],
                        'name' => $productData['name'],
                        'price' => $productData['price'],
                        'type_product_id' => $productData['type_product_id'],
                        'created_at' => $productData['created_at'],
                        'updated_at' => $productData['updated_at'],
                        'type' => [
                            'id' => $productTypeData['id'],
                            'name' => $productTypeData['name'],
                            'created_at' => $productTypeData['created_at'],
                            'updated_at' => $productTypeData['updated_at']
                        ],
                        'tax' => [
                            'id' => $ProductTax['id'],
                            'percentage' => $ProductTax['tax_percentage'],
                            'created_at' => $ProductTax['created_at'],
                            'updated_at' => $ProductTax['updated_at']
                        ]
                    ]
                ];

                $products[] = $product;
            }}
            }

            echo json_encode($products);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["Error: " . $e->getMessage()]);
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
