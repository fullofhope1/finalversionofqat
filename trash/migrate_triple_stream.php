<?php
require_once 'config/db.php';

try {
    $pdo->beginTransaction();

    // 1. Update leftovers table
    echo "Updating leftovers table...\n";
    $pdo->exec("ALTER TABLE leftovers ADD COLUMN unit_type enum('weight', 'قبضة', 'قرطاس') NOT NULL DEFAULT 'weight' AFTER qat_type_id");

    // 2. Update qat_types table
    echo "Updating qat_types table...\n";
    $pdo->exec("ALTER TABLE qat_types 
        ADD COLUMN price_weight decimal(10,2) DEFAULT 0.00, 
        ADD COLUMN price_qabdah decimal(10,2) DEFAULT 0.00, 
        ADD COLUMN price_qartas decimal(10,2) DEFAULT 0.00");

    $pdo->commit();
    echo "Database migrations completed successfully.\n";
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Migration failed: " . $e->getMessage() . "\n";
}
