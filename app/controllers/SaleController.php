<?php

class SaleController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function createSale($date, $total_value, $total_tax, $user_id)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO Cart (total_value, total_tax, user_id) VALUES (?, ?, ?)");
            $stmt->execute([$date, $total_value, $total_tax, $user_id]);

            if ($stmt->rowCount() > 0) {
                return true;
            }
        } catch (PDOException $e) {
            echo "Error creating sale: " . $e->getMessage();
        }

        return false;
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
}