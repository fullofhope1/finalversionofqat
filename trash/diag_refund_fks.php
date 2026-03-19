<?php
require 'config/db.php';
require_once 'includes/Autoloader.php';

header('Content-Type: text/plain; charset=utf-8');

echo "--- Foreign Keys for 'refunds' ---\n";
$sql = "SELECT COLUMN_NAME, REFERENCED_TABLE_NAME 
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_NAME = 'refunds' AND TABLE_SCHEMA = 'qat_erp' 
        AND REFERENCED_TABLE_NAME IS NOT NULL";
$stmt = $pdo->query($sql);
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "--- Customer 50 Check ---\n";
$stmt = $pdo->prepare("SELECT id, name FROM customers WHERE id = 50");
$stmt->execute();
print_r($stmt->fetch(PDO::FETCH_ASSOC));

echo "--- User 33 Check ---\n";
$stmt = $pdo->query("SELECT id, username FROM users WHERE id = 33");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
