<?php
require_once 'config/db.php';

function runSql($pdo, $sql)
{
    echo "Running: $sql\n";
    try {
        $pdo->exec($sql);
        echo "Success.\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// 1. Update leftovers table
runSql($pdo, "ALTER TABLE leftovers ADD COLUMN unit_type enum('weight', 'قبضة', 'قرطاس') NOT NULL DEFAULT 'weight' AFTER qat_type_id");

// 2. Update qat_types table
runSql($pdo, "ALTER TABLE qat_types 
    ADD COLUMN price_weight decimal(10,2) DEFAULT 0.00, 
    ADD COLUMN price_qabdah decimal(10,2) DEFAULT 0.00, 
    ADD COLUMN price_qartas decimal(10,2) DEFAULT 0.00");

echo "Finished.\n";
