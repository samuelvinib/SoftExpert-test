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
        $stmt = $this->db->prepare("INSERT INTO Sale (date, total_value, total_tax, user_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$date, $total_value, $total_tax, $user_id]);

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }


    public function getAllSales()
    {
        $stmt = $this->db->prepare("SELECT * FROM Sale");
        $stmt->execute();
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($sales as $sale) {
            $saleId = $sale['id'];

            // Obter os itens de venda correspondentes ao sale_id da venda atual
            $stmt = $this->db->prepare("SELECT * FROM SaleItem WHERE sale_id = :sale_id");
            $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
            $stmt->execute();
            $saleItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Adicionar a venda e seus itens ao resultado
            $sale['sale_items'] = $saleItems;
            $result[] = $sale;
        }
        return $result;
    }


    public function getSaleById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM Sale WHERE id = ?");
        $stmt->execute([$id]);
        $sale = $stmt->fetch(PDO::FETCH_ASSOC);

            $saleId = $sale['id'];

            // Obter os itens de venda correspondentes ao sale_id da venda atual
            $stmt = $this->db->prepare("SELECT * FROM SaleItem WHERE sale_id = :sale_id");
            $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
            $stmt->execute();
            $saleItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Adicionar a venda e seus itens ao resultado
            $sale['sale_items'] = $saleItems;
            $result[] = $sale;
        return $result;
    }
}
