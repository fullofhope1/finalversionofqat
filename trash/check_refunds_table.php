<?php
require 'config/db.php';
$stmt = $pdo->query("DESCRIBE refunds");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($columns);
echo "</pre>";
