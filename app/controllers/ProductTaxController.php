<?php
class ProductTaxController
{
    private $db;
    
    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function createProductTax($type_product_id, $tax_percentage)
    {
        
        if(!$type_product_id || !$tax_percentage){
            http_response_code(422);
            exit(json_encode(['message' => 'The given data was invalid.',
            'error' => ['The type_product_id field is required.',
            'The tax_percentage field is required.'
            ]
        ]));
        };

        try {
            $stmt = $this->db->prepare("INSERT INTO ProductTax (type_product_id, tax_percentage) VALUES (?, ?)");
            $stmt->execute([$type_product_id, $tax_percentage]);
            
            if ($stmt->rowCount() > 0) {
                http_response_code(201);
                exit(json_encode(["message: " => "New product tax created successfully."]));
            }
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(['message' => 'Error creating a new product tax',
            "error" => $e->getMessage()
        ]));
        }
        
        http_response_code(400);
        exit(json_encode(["message: " => "Failed to create a new product tax."]));
    }
    

    public function getAllProductTaxes()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM ProductTax");
            $stmt->execute();
            $productTaxes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($productTaxes);
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(["message: " => "Error retrieving product taxes",
            'error' => $e->getMessage()
        ]));
        }
    }
    
    public function getProductTaxById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM ProductTax WHERE id = ?");
            $stmt->execute([$id]);
            $productTax = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($productTax) {
                echo $productTax;
            }
            
            http_response_code(400);
            exit(json_encode(["message: " => "Product tax not found in the database."]));
            return false;
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(["message: " => "Error retrieving product tax.",
            'error' => $e->getMessage()
        ]));
        }
    }
    
    public function updateProductTax($id, $type_product_id, $tax_percentage)
    {
        try {
            $stmt = $this->db->prepare("UPDATE ProductTax SET type_product_id = ?, tax_percentage = ? WHERE id = ?");
            $stmt->execute([$type_product_id, $tax_percentage, $id]);
            
            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                exit(json_encode(["message: " => "Product tax updated successfully."]));
            }
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(["message: " => "Error updating product tax.",
            'error' => $e->getMessage()
        ]));
        }
        
        http_response_code(400);
        exit(json_encode(["message: " => "Failed to update product tax."]));
    }
    
    public function deleteProductTax($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM ProductTax WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                exit(json_encode(["message: " => "Product tax deleted successfully."]));
            }
        } catch (PDOException $e) {
            http_response_code(500);
            exit(json_encode(["message: " => "Error deleting product tax.",
            'error' => $e->getMessage()
        ]));
        }
        
        http_response_code(400);
        exit(json_encode(["message: " => "Failed to delete product tax."]));
    }
}
?>