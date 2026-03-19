<?php
require 'config/db.php';

header('Content-Type: text/plain; charset=utf-8');

echo "--- Customer IDs (First 10) ---\n";
$stmt = $pdo->query("SELECT id, name FROM customers LIMIT 10");
print_r($stmt->fetchAll());
