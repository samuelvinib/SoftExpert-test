<?php

class SaleController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function createSale($product_id, $quantity, $price, $tax, $user_id)
    {
        $allCars = $this->getAllCarts($user_id);
        $cartUser = $this->getLastCart($user_id);

            try{
                if( count($allCars) === 0 || $cartUser['completed_purchase'] === true){
                $stmtCart = $this->db->prepare("INSERT INTO Cart (user_id) VALUES (?)");
                $stmtCart->execute([$user_id]);
                }
                $cartUser = $this->getLastCart($user_id);

                $stmtSaleItem = $this->db->prepare("INSERT INTO SaleItem (cart_id, product_id, quantity, item_total_value, tax_amount) VALUES (?, ?, ?, ?, ?)");
                $item_total_value = $quantity * $price;
                $tax_amount = (floatval($item_total_value) * floatval($tax)) / 100;
                $stmtSaleItem->execute([$cartUser['id'], $product_id, $quantity, $item_total_value, $tax_amount ]);

                $stmtDataCart = $this->db->prepare("UPDATE Cart SET total_value = ?, total_tax = ? WHERE id = ?");
                $totalValue = floatval($cartUser['total_value']) + $item_total_value;
                $tax_amount = floatval($cartUser['total_tax']) + $tax_amount;
                $stmtDataCart->execute([$totalValue, $tax_amount, $cartUser['id']]);

                if ($stmtDataCart->rowCount() > 0) {
                    exit(json_encode(['message' => 'Item saved in your cart!']));
                } else {
                    return false; 
                }
            } catch (PDOException $e) {
                exit(json_encode(['message' => 'Error creating cart.', 'error' => $e->getMessage()]));
            }
    }



    public function checkout($id){
        $lastCart = $this->getLastCart($id);
        if($lastCart['completed_purchase'] == false){
            try {
                $stmt = $this->db->prepare("UPDATE Cart SET completed_purchase = ? WHERE id = ?");
                $stmt->execute([true,$lastCart['id']]);

                $stmtCart = $this->db->prepare("INSERT INTO Cart (user_id) VALUES (?)");
                $stmtCart->execute([$id]);

                if ($stmt->rowCount() > 0) {
                    exit(json_encode(['message' => 'completed purchase!']));
                } else {
                    return false; 
                }
            } catch (PDOException $e) {
                exit(json_encode(['message' => 'Error to completed purchase!', 'error' => $e->getMessage()]));
            }
        }else{
            exit(json_encode(['message' => 'You have no open shopping carts!']));
        }
    }

    public function getAllSales()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Cart");
            $stmt->execute();
            $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result = [];

            foreach ($sales as $sale) {
                $saleId = $sale['id'];

                $stmt = $this->db->prepare("SELECT * FROM SaleItem WHERE sale_id = :sale_id");
                $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
                $stmt->execute();
                $saleItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $sale['sale_items'] = $saleItems;
                $result[] = $sale;
            }

            return $result;
        } catch (PDOException $e) {
            echo "Error getting sales: " . $e->getMessage();
            return [];
        }
    }

    public function getSaleById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Cart WHERE id = ?");
            $stmt->execute([$id]);
            $sale = $stmt->fetch(PDO::FETCH_ASSOC);

            $result = [];

            if ($sale) {
                $saleId = $sale['id'];

                $stmt = $this->db->prepare("SELECT * FROM SaleItem WHERE sale_id = :sale_id");
                $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
                $stmt->execute();
                $saleItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $sale['sale_items'] = $saleItems;
                $result[] = $sale;
            }

            return $result;
        } catch (PDOException $e) {
            echo "Error getting sale by ID: " . $e->getMessage();
            return [];
        }
    }

    public function getAllCarts($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Cart WHERE user_id = ?");
            $stmt->execute([$id]);
            $carts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $carts;
        } catch (PDOException $e) {
            exit(json_encode(['message' => 'Error getting Cart', 'error' => $e->getMessage()]));
        }
    }

    public function getLastCart($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Cart WHERE user_id = ? ORDER BY id DESC LIMIT 1");
            $stmt->execute([$id]);
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
        
            $stmtSaleItems = $this->db->prepare("SELECT si.*, p.* FROM SaleItem si
                JOIN Product p ON si.product_id = p.id
                WHERE si.cart_id = ?");
            $stmtSaleItems->execute([$cart['id']]);
            $saleItems = $stmtSaleItems->fetchAll(PDO::FETCH_ASSOC);
        
            $cart['sale_items'] = [];
        
            foreach ($saleItems as $saleItem) {
                $product = [
                    'id' => $saleItem['product_id'],
                    'name' => $saleItem['name'],
                    'image_path' => $saleItem['image_path'],
                    'price' => $saleItem['price'],
                    // Adicione outros detalhes do produto conforme necessário
                ];
        
                $cart['sale_items'][] = [
                    'id' => $saleItem['id'],
                    'cart_id' => $saleItem['cart_id'],
                    'product_id' => $saleItem['product_id'],
                    'quantity' => $saleItem['quantity'],
                    'item_total_value' => $saleItem['item_total_value'],
                    'tax_amount' => $saleItem['tax_amount'],
                    'created_at' => $saleItem['created_at'],
                    'updated_at' => $saleItem['updated_at'],
                    'product' => $product,
                ];
            }
        
            return $cart;
        } catch (PDOException $e) {
            exit(json_encode(['message' => 'Error getting Cart', 'error' => $e->getMessage()]));
        }
        
    }
}