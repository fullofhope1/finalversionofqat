<?php
require 'config/db.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $pdo->beginTransaction();

    // 1. Create new customer
    $pdo->query("INSERT INTO customers (name, total_debt) VALUES ('Test Customer', 100)");
    $newCustId = $pdo->lastInsertId();
    echo "New Customer ID: $newCustId\n";

    // 2. Try to insert refund
    $userId = 33; // VALID super_admin
    $sql = "INSERT INTO refunds (customer_id, amount, refund_type, reason, created_by, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $res = $stmt->execute([$newCustId, 10, 'Debt', 'Test New Cust', $userId]);
    echo "Refund INSERT Result: Success\n";

    $pdo->rollBack();
    echo "Rollback successful.\n";
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    echo "FAILED: " . $e->getMessage() . "\n";
}
