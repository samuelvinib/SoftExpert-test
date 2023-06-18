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
        try {
            $stmt = $this->db->prepare("INSERT INTO Product (name, price, type_product_id) VALUES (?, ?, ?)");
            $stmt->execute([$name, $price, $type_product_id]);

            if ($stmt->rowCount() > 0) {
                http_response_code(201);
                echo "New product created successfully.";
                return true;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error: " . $e->getMessage();
        }

        return false;
    }

    public function getAllProducts()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Product");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $products;
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    public function getProductById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Product WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            return $product;
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    public function updateProduct($id, $name, $price, $type_product_id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE Product SET name = ?, price = ?, type_product_id = ? WHERE id = ?");
            $stmt->execute([$name, $price, $type_product_id, $id]);
    
            if ($stmt->rowCount() > 0) {
                echo "Product updated successfully.";
                return true;
            }
            
            http_response_code(400);
            echo "Product not found in database.";
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error: " . $e->getMessage();
        }
    
        return false;
    }
    

    public function deleteProduct($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM Product WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                echo "Product deleted successfully.";
                return true;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error deleting product: " . $e->getMessage();
        }

        http_response_code(400);
        echo "Failed to delete product.";
        return false;
    }
}