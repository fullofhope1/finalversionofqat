<?php
require 'config/db.php';

header('Content-Type: text/plain; charset=utf-8');

$custId = 50;
$userId = 33;

echo "--- Direct INSERT Test ---\n";
try {
    $pdo->beginTransaction();
    $sql = "INSERT INTO refunds (customer_id, amount, refund_type, reason, created_by, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $res = $stmt->execute([$custId, 10, 'Debt', 'Direct Test', $userId]);
    echo "INSERT Result: " . ($res ? "Success" : "Success (false return?)") . "\n";

    $lastId = $pdo->lastInsertId();
    echo "New Refund ID: $lastId\n";

    $pdo->rollBack();
    echo "Rollback successful.\n";
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    echo "FAILED: " . $e->getMessage() . "\n";
}
