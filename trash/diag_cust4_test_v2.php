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
    'reason' => 'Fixing ID mismatch test - No Outer Tx'
];

try {
    // We let the service handle the transaction
    if ($service->processRefund($data, $userId)) {
        echo "SUCCESS: processRefund returned true.\n";

        // Check results
        $stmt = $pdo->prepare("SELECT * FROM refunds WHERE customer_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$custId]);
        $refund = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Created Refund: ";
        print_r($refund);

        // Clean up (since we are not in a transaction anymore, we have to manual delete or just leave it)
        $pdo->exec("DELETE FROM refunds WHERE id = " . $refund['id']);
        echo "Cleaned up test refund.\n";
    }
} catch (Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
