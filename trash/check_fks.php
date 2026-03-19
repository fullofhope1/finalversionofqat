<?php
require 'config/db.php';
$sql = "SELECT 
  COLUMN_NAME, 
  CONSTRAINT_NAME, 
  REFERENCED_TABLE_NAME, 
  REFERENCED_COLUMN_NAME
FROM
  INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE
  REFERENCED_TABLE_SCHEMA = 'qat_erp' AND
  TABLE_NAME = 'refunds'";

$stmt = $pdo->query($sql);
$fks = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "--- Foreign Keys for 'refunds' ---\n";
foreach ($fks as $fk) {
    echo "Column: {$fk['COLUMN_NAME']} -> Ref Table: {$fk['REFERENCED_TABLE_NAME']}({$fk['REFERENCED_COLUMN_NAME']}) [Name: {$fk['CONSTRAINT_NAME']}]\n";
}
