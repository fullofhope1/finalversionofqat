<?php
require_once 'config/db.php';

// Target types
$target_types = [
    'جمام نقوة',
    'جمام كالف',
    'جمام سمين',
    'جمام قصار',
    'صدور نقوة',
    'صدور عادي',
    'قطل'
];

// Get current types and their usage
$stmt = $pdo->query("
    SELECT t.id, t.name, 
           (SELECT COUNT(*) FROM sales s WHERE s.qat_type_id = t.id) as sales_count,
           (SELECT COUNT(*) FROM purchases p WHERE p.qat_type_id = t.id) as purchases_count
    FROM qat_types t
");
$current_types = $stmt->fetchAll();

echo "Current Types and Usage:\n";
foreach ($current_types as $t) {
    echo "ID: {$t['id']} | Name: {$t['name']} | Sales: {$t['sales_count']} | Purchases: {$t['purchases_count']}\n";
}
