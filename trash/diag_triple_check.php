<?php
require 'config/db.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $pdo->beginTransaction();

    $custId = 50;

    // 1. Verify customer 50 exists RIGHT NOW
    $stmt = $pdo->prepare("SELECT id, name FROM customers WHERE id = ?");
    $stmt->execute([$custId]);
    $cust = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($cust) {
        echo "Customer 50 found: " . $cust['name'] . "\n";
    } else {
        echo "Customer 50 NOT FOUND!\n";
    }

    // 2. Try to insert refund
    $userId = 33; // VALID super_admin
    $sql = "INSERT INTO refunds (customer_id, amount, refund_type, reason, created_by, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $res = $stmt->execute([$custId, 10, 'Debt', 'Triple Check', $userId]);
    echo "Refund INSERT Result: Success\n";

    $pdo->rollBack();
    echo "Rollback successful.\n";
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    echo "FAILED: " . $e->getMessage() . "\n";
}
