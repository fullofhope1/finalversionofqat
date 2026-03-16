<?php
require_once 'config/db.php';
$tables = ['purchases', 'sales', 'leftovers', 'qat_types'];
$output = "";
foreach ($tables as $table) {
    $output .= "--- $table ---\n";
    $stmt = $pdo->query("SHOW CREATE TABLE $table");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $output .= $row['Create Table'] . "\n\n";
}
file_put_contents('schema_dump.txt', $output);
echo "Dumped to schema_dump.txt\n";
