<?php
// migrate_notes_col.php
require 'config/db.php';

try {
    // 1. Check leftovers
    $checkL = $pdo->query("SHOW COLUMNS FROM leftovers LIKE 'notes'");
    if ($checkL->rowCount() == 0) {
        $pdo->exec("ALTER TABLE leftovers ADD COLUMN notes TEXT AFTER sale_date");
        echo "Added 'notes' to leftovers table.<br>";
    }

    // 2. Check sales
    $checkS = $pdo->query("SHOW COLUMNS FROM sales LIKE 'notes'");
    if ($checkS->rowCount() == 0) {
        $pdo->exec("ALTER TABLE sales ADD COLUMN notes TEXT AFTER is_paid");
        echo "Added 'notes' to sales table.<br>";
    }

    echo "Migration finished.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
