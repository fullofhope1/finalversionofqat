<?php
require 'config/db.php';
try {
    foreach (['leftovers', 'sales'] as $table) {
        $check = $pdo->query("SHOW COLUMNS FROM $table LIKE 'notes'");
        echo "Table $table has 'notes' column: " . ($check->rowCount() > 0 ? "YES" : "NO") . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
