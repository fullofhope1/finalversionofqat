<?php
require 'config/db.php';
require_once 'includes/Autoloader.php';

header('Content-Type: text/plain; charset=utf-8');

// Target Customer: 4بكيل (ID: 50)
$custId = 50;
$amount = 1000;
$type = 'Debt';
$reason = 'Test Diagnosis';
$userId = 1; // Assuming admin user

echo "--- Simulating processRefund for Customer ID $custId ---\n";

$refundRepo = new RefundRepository($pdo);
$customerRepo = new CustomerRepository($pdo);
$service = new RefundService($refundRepo, $customerRepo);

$data = [
    'customer_id' => $custId,
    'amount' => $amount,
    'refund_type' => $type,
    'reason' => $reason
];

try {
    $pdo->beginTransaction(); // Outer transaction to rollback at end of test
    if ($service->processRefund($data, $userId)) {
        echo "Success: processRefund returned true.\n";

        // Check if refund was created
        $stmt = $pdo->prepare("SELECT * FROM refunds WHERE customer_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$custId]);
        $refund = $stmt->fetch();
        echo "Refund Record Created: ";
        print_r($refund);

        // Check if sales were updated
        $stmt = $pdo->prepare("SELECT id, refund_amount FROM sales WHERE customer_id = ? AND refund_amount > 0 ORDER BY id DESC");
        $stmt->execute([$custId]);
        $sales = $stmt->fetchAll();
        echo "Updated Sales: ";
        print_r($sales);

        // Check if customer debt was updated
        $stmt = $pdo->prepare("SELECT total_debt FROM customers WHERE id = ?");
        $stmt->execute([$custId]);
        echo "Customer Balance: " . $stmt->fetchColumn() . "\n";
    }
    $pdo->rollBack(); // Don't actually keep the test data
    echo "Rollback successful (test finished).\n";
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo "Caught Exception: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
