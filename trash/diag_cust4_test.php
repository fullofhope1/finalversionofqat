<?php
require 'config/db.php';
require_once 'includes/Autoloader.php';

header('Content-Type: text/plain; charset=utf-8');

$custId = 4;
$userId = 33; // super_admin

echo "--- Simulating processRefund for Customer 4 (بكيل) ---\n";

$refundRepo = new RefundRepository($pdo);
$customerRepo = new CustomerRepository($pdo);
$service = new RefundService($refundRepo, $customerRepo);

$data = [
    'customer_id' => $custId,
    'amount' => 10,
    'refund_type' => 'Debt',
    'reason' => 'Fixing ID mismatch test'
];

try {
    $pdo->beginTransaction();
    if ($service->processRefund($data, $userId)) {
        echo "SUCCESS: processRefund returned true.\n";
    }
    $pdo->rollBack();
    echo "Rollback successful.\n";
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    echo "FAILED: " . $e->getMessage() . "\n";
}
