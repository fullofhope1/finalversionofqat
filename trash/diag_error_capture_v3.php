<?php
require 'config/db.php';
require_once 'includes/Autoloader.php';

$data = ['customer_id' => 50, 'amount' => 1, 'refund_type' => 'Debt', 'reason' => 'test-v3'];
$userId = 33; // VALID super_admin ID

try {
    $refundRepo = new RefundRepository($pdo);
    $customerRepo = new CustomerRepository($pdo);
    $service = new RefundService($refundRepo, $customerRepo);
    $service->processRefund($data, $userId);
    file_put_contents('last_success.txt', "OK - " . date('Y-m-d H:i:s'));
    echo "OK";
} catch (Exception $e) {
    file_put_contents('last_error.txt', $e->getMessage() . "\n" . $e->getTraceAsString());
    echo "ERROR: " . $e->getMessage();
}
