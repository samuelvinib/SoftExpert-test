<?php

require_once './app/middleware/TokenMiddleware.php';

class ProductTypeController
{
    private $db;
    
    public function __construct()
    {
        $this->db = (new Database())->connect();
    }
    
    public function createProductType($name)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO ProductType (name) VALUES (?)");
            $stmt->execute([$name]);

            if ($stmt->rowCount() > 0) {
                http_response_code(201);
                echo "New product type created successfully.";
                return true;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error creating a new product type: " . $e->getMessage();
            return false;
        }
        
        http_response_code(400);
        echo "Failed to create a new product type.";
        return false;
    }

    public function getAllProductTypes()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM ProductType");
            $stmt->execute();
            $productTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $productTypes;
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error retrieving product types: " . $e->getMessage();
            return false;
        }
    }
    
    public function getProductTypeById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM ProductType WHERE id = ?");
            $stmt->execute([$id]);
            $productType = $stmt->fetch(PDO::FETCH_ASSOC);
            if($productType){
                return $productType;
            }
            http_response_code(400);
            return "product not found in bank";
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error retrieving product type: " . $e->getMessage();
            return false;
        }
    }
    
    public function updateProductType($id, $name)
    {
        try {
            $stmt = $this->db->prepare("UPDATE ProductType SET name = ? WHERE id = ?");
            $stmt->execute([$name, $id]);

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                echo "Product type updated successfully.";
                return true;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error updating product type: " . $e->getMessage();
            return false;
        }
        
        http_response_code(400);
        echo "Failed to update product type.";
        return false;
    }
    

    public function deleteProductType($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM ProductType WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                echo "Product type deleted successfully.";
                return true;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error deleting product type: " . $e->getMessage();
            return false;
        }
        
        http_response_code(400);
        echo "Failed to delete product type.";
        return false;
    }
}
