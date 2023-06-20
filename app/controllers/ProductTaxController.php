<?php
class ProductTaxController
{
    private $db;
    
    public function __construct()
    {
        $this->db = (new Database())->connect();
    }
    
    // Method to create a new product tax
    public function createProductTax($type_product_id, $tax_percentage)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO ProductTax (type_product_id, tax_percentage) VALUES (?, ?)");
            $stmt->execute([$type_product_id, $tax_percentage]);
            
            if ($stmt->rowCount() > 0) {
                http_response_code(201);
                echo "New product tax created successfully.";
                return true;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error creating a new product tax: " . $e->getMessage();
            return false;
        }
        
        http_response_code(400);
        echo "Failed to create a new product tax.";
        return false;
    }
    
    // Method to get all product taxes
    public function getAllProductTaxes()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM ProductTax");
            $stmt->execute();
            $productTaxes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $productTaxes;
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error retrieving product taxes: " . $e->getMessage();
            return false;
        }
    }
    
    // Method to get a product tax by ID
    public function getProductTaxById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM ProductTax WHERE id = ?");
            $stmt->execute([$id]);
            $productTax = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($productTax) {
                return $productTax;
            }
            
            http_response_code(400);
            echo "Product tax not found in the database.";
            return false;
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error retrieving product tax: " . $e->getMessage();
            return false;
        }
    }
    
    // Method to update a product tax
    public function updateProductTax($id, $type_product_id, $tax_percentage)
    {
        try {
            $stmt = $this->db->prepare("UPDATE ProductTax SET type_product_id = ?, tax_percentage = ? WHERE id = ?");
            $stmt->execute([$type_product_id, $tax_percentage, $id]);
            
            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                echo "Product tax updated successfully.";
                return true;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error updating product tax: " . $e->getMessage();
            return false;
        }
        
        http_response_code(400);
        echo "Failed to update product tax.";
        return false;
    }
    
    // Method to delete a product tax
    public function deleteProductTax($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM ProductTax WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                echo "Product tax deleted successfully.";
                return true;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error deleting product tax: " . $e->getMessage();
            return false;
        }
        
        http_response_code(400);
        echo "Failed to delete product tax.";
        return false;
    }
}
?>