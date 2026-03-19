<?php
require 'config/db.php';
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'refunds'");
    $tableExists = $stmt->rowCount() > 0;
    echo "Table 'refunds' exists: " . ($tableExists ? 'Yes' : 'No') . "\n";
    if ($tableExists) {
        $stmt = $pdo->query("DESCRIBE refunds");
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
