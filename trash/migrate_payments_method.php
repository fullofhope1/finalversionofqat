<?php
require 'config/db.php';
try {
    $check = $pdo->query("SHOW COLUMNS FROM payments LIKE 'payment_method'");
    if ($check->rowCount() == 0) {
        $pdo->exec("ALTER TABLE payments ADD COLUMN payment_method ENUM('Cash', 'Transfer') DEFAULT 'Cash' AFTER amount");
        echo "Column 'payment_method' added to payments table.\n";
    } else {
        echo "Column 'payment_method' already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
