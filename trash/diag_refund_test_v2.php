<?php
require 'config/db.php';
require_once 'includes/Autoloader.php';

header('Content-Type: text/plain; charset=utf-8');

$custId = 50;
$amount = 10;
$type = 'Debt';
$reason = 'Final Test Diagnosis';
$userId = 33; // VALID super_admin ID

echo "--- Simulating processRefund for Customer 50 with User 33 ---\n";

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
    $pdo->beginTransaction();
    if ($service->processRefund($data, $userId)) {
        echo "SUCCESS: processRefund returned true.\n";

        $stmt = $pdo->prepare("SELECT * FROM refunds WHERE customer_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$custId]);
        print_r($stmt->fetch(PDO::FETCH_ASSOC));
    }
    $pdo->rollBack();
    echo "Rollback successful.\n";
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    echo "FAILED: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
