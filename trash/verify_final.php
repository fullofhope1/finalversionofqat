<?php
require_once 'config/db.php';
$stmt = $pdo->query("SELECT name FROM qat_types");
$names = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Final Types count: " . count($names) . "\n";
foreach ($names as $name) {
    echo "- $name\n";
}
