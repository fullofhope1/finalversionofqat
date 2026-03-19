<?php
require 'config/db.php';
require_once 'includes/Autoloader.php';

$data = ['customer_id' => 50, 'amount' => 1, 'refund_type' => 'Debt', 'reason' => 'test'];
$userId = 1;

try {
    $refundRepo = new RefundRepository($pdo);
    $customerRepo = new CustomerRepository($pdo);
    $service = new RefundService($refundRepo, $customerRepo);
    $service->processRefund($data, $userId);
    echo "OK";
} catch (Exception $e) {
    file_put_contents('last_error.txt', $e->getMessage() . "\n" . $e->getTraceAsString());
    echo "ERROR: " . $e->getMessage();
}
