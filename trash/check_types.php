<?php
require_once 'config/db.php';
$stmt = $pdo->query("SELECT * FROM qat_types");
$types = $stmt->fetchAll();
header('Content-Type: text/plain; charset=utf-8');
foreach ($types as $type) {
    echo "ID: " . $type['id'] . " | Name: " . $type['name'] . "\n";
}
