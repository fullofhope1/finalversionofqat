<?php
// includes/classes/RefundRepository.php

class RefundRepository extends BaseRepository
{
    public function getRecentRefunds($limit = 50)
    {
        $limit = (int)$limit;
        $sql = "SELECT r.*, c.name as cust_name
                FROM refunds r
                LEFT JOIN customers c ON r.customer_id = c.id
                ORDER BY r.id DESC LIMIT $limit";
        return $this->fetchAll($sql);
    }

    public function create($data)
    {
        $sql = "INSERT INTO refunds (customer_id, amount, refund_type, reason, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())";
        return $this->execute($sql, [
            $data['customer_id'],
            $data['amount'],
            $data['refund_type'],
            $data['reason'],
            $data['created_by'] ?? null
        ]);
    }

    public function getRefundsByPeriod($where, $params)
    {
        $sql = "SELECT r.*, c.name as cust_name FROM refunds r LEFT JOIN customers c ON r.customer_id = c.id $where ORDER BY r.id DESC";
        return $this->fetchAll($sql, $params);
    }

    public function getUnpaidSalesWithBalance($customerId)
    {
        $sql = "SELECT id, (price - paid_amount - refund_amount) as remaining_debt
                FROM sales
                WHERE customer_id = ? AND is_paid = 0
                ORDER BY sale_date DESC, id DESC";
        return $this->fetchAll($sql, [$customerId]);
    }

    public function applyRefundToSale($saleId, $amount)
    {
        $sql = "UPDATE sales SET refund_amount = refund_amount + ? WHERE id = ?";
        return $this->execute($sql, [$amount, $saleId]);
    }
}
