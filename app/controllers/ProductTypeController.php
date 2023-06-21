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
                exit(json_encode(['message' => 'New product type created successfully.']));
            }
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(['message' => "Error creating a new product type.",'error' => $e->getMessage()]));
        }
        
        http_response_code(400);
        exit(json_encode(['message' => "Failed to create a new product type."]));
    }

    public function getAllProductTypes()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM ProductType");
            $stmt->execute();
            $productTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            exit(json_encode($productTypes));
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(['message' => "Error retrieving product types.",
            'error' => $e->getMessage()
        ]));
        }
    }
    
    public function getProductTypeById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM ProductType WHERE id = ?");
            $stmt->execute([$id]);
            $productType = $stmt->fetch(PDO::FETCH_ASSOC);
            if($productType){
                exit(json_encode($productType));
            }
            http_response_code(400);
            exit(json_encode(['message' => "product not found in bank"]));
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(['message' => "Error retrieving product type.",
            'error' => $e->getMessage()
        ]));
        }
    }
    
    public function updateProductType($id, $name)
    {
        try {
            $stmt = $this->db->prepare("UPDATE ProductType SET name = ? WHERE id = ?");
            $stmt->execute([$name, $id]);

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                exit(json_encode(['message' => "Product type updated successfully."]));
            }
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(['message' => "Error updating product type.",
            'error' => $e->getMessage()
        ]));
        }
        
        http_response_code(400);
        exit(json_encode(['message' => "Failed to update product type."]));
    }
    

    public function deleteProductType($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM ProductType WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                exit(json_encode(['message' => "Product type deleted successfully."]));
            }
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(['message' => "Error deleting product type.",
            'error' => $e->getMessage()
        ]));
        }
        
        http_response_code(400);
        exit(json_encode(['message' => "Product type does not exist in the database."]));
    }
}
