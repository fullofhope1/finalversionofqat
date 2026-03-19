<?php
require 'config/db.php';
require_once 'includes/Autoloader.php';

header('Content-Type: text/plain; charset=utf-8');

echo "--- All Customers in qat_erp ---\n";
$stmt = $pdo->query("SELECT id, name FROM customers ORDER BY id");
while ($r = $stmt->fetch()) {
    echo $r['id'] . " - " . $r['name'] . "\n";
}

echo "\n--- Recent Refunds in qat_erp ---\n";
$stmt = $pdo->query("SELECT r.*, c.name FROM refunds r LEFT JOIN customers c ON r.customer_id = c.id ORDER BY r.id DESC LIMIT 5");
print_r($stmt->fetchAll());
