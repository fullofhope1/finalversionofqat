<?php
// includes/classes/SaleRepository.php

class SaleRepository extends BaseRepository
{

    public function create(array $data)
    {
        $defaults = [
            'sale_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d'),
            'customer_id' => null,
            'qat_type_id' => null,
            'purchase_id' => null,
            'leftover_id' => null,
            'qat_status' => 'Tari',
            'weight_grams' => 0,
            'unit_type' => 'weight',
            'quantity_units' => 0,
            'price' => 0,
            'payment_method' => 'Cash',
            'is_paid' => 1,
            'transfer_sender' => null,
            'transfer_receiver' => null,
            'transfer_number' => null,
            'transfer_company' => null,
            'debt_type' => null,
            'notes' => ''
        ];
        $data = array_merge($defaults, $data);

        $sql = "INSERT INTO sales (
            sale_date, due_date, customer_id, qat_type_id, purchase_id, leftover_id, 
            qat_status, weight_grams, unit_type, quantity_units, price, payment_method, is_paid, 
            transfer_sender, transfer_receiver, transfer_number, transfer_company, 
            debt_type, notes
        ) VALUES (
            :sale_date, :due_date, :customer_id, :qat_type_id, :purchase_id, :leftover_id, 
            :qat_status, :weight_grams, :unit_type, :quantity_units, :price, :payment_method, :is_paid, 
            :transfer_sender, :transfer_receiver, :transfer_number, :transfer_company, 
            :debt_type, :notes
        )";

        // Only bind keys that exist in the SQL to avoid HY093
        $allowed = array_keys($defaults);
        $filtered = array_intersect_key($data, array_flip($allowed));

        $this->execute($sql, $filtered);
        return $this->pdo->lastInsertId();
    }

    public function getSoldKgByPurchaseId($purchaseId)
    {
        return $this->fetchColumn("SELECT SUM(weight_kg) FROM sales WHERE purchase_id = ?", [$purchaseId]) ?: 0;
    }

    public function getSoldUnitsByPurchaseId($purchaseId)
    {
        return $this->fetchColumn("SELECT SUM(quantity_units) FROM sales WHERE purchase_id = ?", [$purchaseId]) ?: 0;
    }

    public function getSoldKgByLeftoverId($leftoverId)
    {
        return $this->fetchColumn("SELECT SUM(weight_kg) FROM sales WHERE leftover_id = ?", [$leftoverId]) ?: 0;
    }

    public function getSoldUnitsByLeftoverId($leftoverId)
    {
        return $this->fetchColumn("SELECT SUM(quantity_units) FROM sales WHERE leftover_id = ?", [$leftoverId]) ?: 0;
    }

    public function getTodaySalesMapByPurchase($date)
    {
        $sql = "SELECT purchase_id, SUM(weight_kg) as sold_kg 
                FROM sales 
                WHERE sale_date = ? AND purchase_id IS NOT NULL 
                GROUP BY purchase_id";
        return $this->pdo->prepare($sql)->execute([$date]) ? $this->pdo->prepare($sql)->fetchAll(PDO::FETCH_KEY_PAIR) : [];
    }

    // Improved fetch key pair helper needed in BaseRepository or handled here
    public function getSalesMap($date)
    {
        $stmt = $this->pdo->prepare("SELECT purchase_id, SUM(weight_kg) as sold_kg, SUM(quantity_units) as sold_units FROM sales WHERE sale_date = ? AND purchase_id IS NOT NULL GROUP BY purchase_id");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Changed from FETCH_KEY_PAIR as we now have two values per ID
    }
}
