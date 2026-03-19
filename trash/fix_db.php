<?php
require 'config/db.php';
try {
    $pdo->exec("ALTER TABLE payments ADD COLUMN payment_method ENUM('Cash', 'Transfer') DEFAULT 'Cash' AFTER amount");
    echo "Column added successfully!";
} catch (Exception $e) {
    echo "Error or column already exists: " . $e->getMessage();
}
