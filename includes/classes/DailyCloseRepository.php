<?php
// includes/classes/DailyCloseRepository.php

class DailyCloseRepository extends BaseRepository
{
    /**
     * STEP 1: Identification of ANY active leftover items that must be cleared/trashed.
     * We look for EVERYTHING currently active in 'Momsi' or 'Leftover' state 
     * to ensure a fresh start.
     */
    public function getActiveMomsiStock()
    {
        // Legacy support: identify any leftover status in purchases table that wasn't closed
        $sql = "SELECT id, qat_type_id, quantity_kg, received_units, unit_type, purchase_date FROM purchases 
                WHERE status = 'Momsi'";
        return $this->fetchAll($sql);
    }

    public function getActiveManualLeftovers()
    {
        // Identify ALL active leftovers to be trashed
        $sql = "SELECT id, purchase_id, qat_type_id, weight_kg, quantity_units, unit_type, source_date 
                FROM leftovers 
                WHERE status IN ('Transferred_Next_Day', 'Auto_Momsi')";
        return $this->fetchAll($sql);
    }

    public function trashLeftover($leftoverId, $currentDate)
    {
        // Get details for waste recording
        $stmt = $this->pdo->prepare("SELECT * FROM leftovers WHERE id = ?");
        $stmt->execute([$leftoverId]);
        $l = $stmt->fetch();
        if (!$l) return;

        // Calculate surplus (what remains)
        $surplusKg = 0;
        $surplusUnits = 0;
        if ($l['unit_type'] === 'weight') {
            $sold = (float)$this->fetchColumn("SELECT SUM(weight_kg) FROM sales WHERE leftover_id = ?", [$leftoverId]) ?: 0;
            $surplusKg = (float)$l['weight_kg'] - $sold;
        } else {
            $sold = (int)$this->fetchColumn("SELECT SUM(quantity_units) FROM sales WHERE leftover_id = ?", [$leftoverId]) ?: 0;
            $surplusUnits = (int)$l['quantity_units'] - $sold;
        }

        if ($surplusKg > 0.001 || $surplusUnits > 0) {
            // Record as Talf (Auto_Dropped)
            $this->pdo->prepare("INSERT INTO leftovers (source_date, purchase_id, qat_type_id, weight_kg, quantity_units, unit_type, status, decision_date, sale_date) 
                                 VALUES (?, ?, ?, ?, ?, ?, 'Auto_Dropped', ?, ?)")
                ->execute([$l['source_date'], $l['purchase_id'], $l['qat_type_id'], $surplusKg, $surplusUnits, $l['unit_type'], $currentDate, $currentDate]);
        }

        // Close the record
        return $this->pdo->prepare("UPDATE leftovers SET status = 'Dropped' WHERE id = ?")->execute([$leftoverId]);
    }

    public function trashMomsiPurchase($purchaseId, $currentDate)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM purchases WHERE id = ?");
        $stmt->execute([$purchaseId]);
        $p = $stmt->fetch();
        if (!$p) return;

        if ($p['unit_type'] === 'weight') {
            $stmtW = $this->pdo->prepare("SELECT 
                (SELECT SUM(weight_kg) FROM sales WHERE purchase_id = ?) as sold,
                (SELECT SUM(weight_kg) FROM leftovers WHERE purchase_id = ? AND status IN ('Dropped', 'Transferred_Next_Day', 'Auto_Momsi')) as managed");
            $stmtW->execute([$purchaseId, $purchaseId]);
            $row = $stmtW->fetch();
            $surplus = (float)$p['quantity_kg'] - (float)($row['sold'] ?? 0) - (float)($row['managed'] ?? 0);
            $surplusUnits = 0;
        } else {
            $stmtU = $this->pdo->prepare("SELECT 
                (SELECT SUM(quantity_units) FROM sales WHERE purchase_id = ?) as sold,
                (SELECT SUM(quantity_units) FROM leftovers WHERE purchase_id = ? AND status IN ('Dropped', 'Transferred_Next_Day', 'Auto_Momsi')) as managed");
            $stmtU->execute([$purchaseId, $purchaseId]);
            $row = $stmtU->fetch();
            $surplusUnits = (int)$p['received_units'] - (int)($row['sold'] ?? 0) - (int)($row['managed'] ?? 0);
            $surplus = 0;
        }

        if ($surplus > 0.001 || $surplusUnits > 0) {
            // Record as Waste
            $sql = "INSERT INTO leftovers (source_date, purchase_id, qat_type_id, weight_kg, quantity_units, unit_type, status, decision_date, sale_date) 
                    VALUES (?, ?, ?, ?, ?, ?, 'Auto_Dropped', ?, ?)";
            $this->pdo->prepare($sql)->execute([
                $p['purchase_date'],
                $purchaseId,
                $p['qat_type_id'],
                $surplus,
                $surplusUnits,
                $p['unit_type'],
                $currentDate,
                $currentDate
            ]);
        }

        // Close it
        return $this->pdo->prepare("UPDATE purchases SET status = 'Closed' WHERE id = ?")->execute([$purchaseId]);
    }

    /**
     * STEP 2: Moving today's surplus Fresh stock.
     */
    public function getDayFreshStock($currentDate)
    {
        $stmt = $this->pdo->prepare("SELECT id, qat_type_id, quantity_kg, received_units, unit_type, purchase_date FROM purchases 
                                    WHERE purchase_date = ? AND status = 'Fresh'");
        $stmt->execute([$currentDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSoldAndManagedForPurchase($purchaseId)
    {
        $stmt = $this->pdo->prepare("SELECT 
            (SELECT SUM(weight_kg) FROM sales WHERE purchase_id = ?) as sold_kg,
            (SELECT SUM(weight_kg) FROM leftovers WHERE purchase_id = ? AND status IN ('Dropped', 'Transferred_Next_Day', 'Auto_Momsi')) as managed_kg,
            (SELECT SUM(quantity_units) FROM sales WHERE purchase_id = ?) as sold_units,
            (SELECT SUM(quantity_units) FROM leftovers WHERE purchase_id = ? AND status IN ('Dropped', 'Transferred_Next_Day', 'Auto_Momsi')) as managed_units");
        $stmt->execute([$purchaseId, $purchaseId, $purchaseId, $purchaseId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'sold_kg' => $row['sold_kg'] ?: 0,
            'managed_kg' => $row['managed_kg'] ?: 0,
            'sold_units' => $row['sold_units'] ?: 0,
            'managed_units' => $row['managed_units'] ?: 0
        ];
    }

    public function moveStockToTomorrow($purchaseId, $surplusKg, $surplusUnits, $currentDate, $tomorrow)
    {
        // 1. Create entry in leftovers table (This is now the ONLY record for next-day stock)
        $sqlL = "INSERT INTO leftovers (source_date, purchase_id, qat_type_id, weight_kg, quantity_units, unit_type, status, decision_date, sale_date) 
                 VALUES (?, ?, (SELECT qat_type_id FROM purchases WHERE id = ?), ?, ?, (SELECT unit_type FROM purchases WHERE id = ?), 'Auto_Momsi', ?, ?)";
        $this->pdo->prepare($sqlL)->execute([$currentDate, $purchaseId, $purchaseId, $surplusKg, $surplusUnits, $purchaseId, $currentDate, $tomorrow]);

        // 2. Close original purchase
        $this->pdo->prepare("UPDATE purchases SET status = 'Closed' WHERE id = ?")->execute([$purchaseId]);
    }

    /**
     * STEP 3: Debt Rollover
     * Moves all unpaid Daily debts to Deferred (مؤجل) status and updates due_date.
     */
    public function migrateDailyDebts($currentDate, $tomorrow)
    {
        $sql = "UPDATE sales
                SET due_date = ?,
                    debt_type = 'Deferred'
                WHERE due_date <= ?
                AND payment_method = 'Debt'
                AND (debt_type = 'Daily' OR debt_type IS NULL OR debt_type = '')
                AND is_paid = 0";
        return $this->pdo->prepare($sql)->execute([$tomorrow, $currentDate]);
    }

    public function closeLegacyMomsiPurchases()
    {
        // Cleanup: Any purchase still in 'Momsi' status should be closed to avoid accounting confusion
        $sql = "UPDATE purchases SET status = 'Closed' WHERE status = 'Momsi'";
        return $this->pdo->prepare($sql)->execute();
    }

    public function closePurchase($purchaseId)
    {
        return $this->pdo->prepare("UPDATE purchases SET status = 'Closed' WHERE id = ?")->execute([$purchaseId]);
    }
}
