<?php
require_once 'config/db.php';

$target_names = [
    'جمام نقوة',
    'جمام كالف',
    'جمام سمين',
    'جمام قصار',
    'صدور نقوة',
    'صدور عادي',
    'قطل'
];

// Get ID of a safe target for mapping
$stmt = $pdo->prepare("SELECT id FROM qat_types WHERE name = 'جمام نقوة' LIMIT 1");
$stmt->execute();
$safe_id = $stmt->fetchColumn();

if (!$safe_id) die("Critical Error: 'جمام نقوة' not found.\n");

// Get all types
$stmt = $pdo->query("SELECT id, name FROM qat_types");
$all = $stmt->fetchAll();

foreach ($all as $t) {
    if (!in_array($t['name'], $target_names)) {
        echo "Processing deletion for: {$t['name']} (ID {$t['id']})\n";

        // Re-map ANY data
        $pdo->prepare("UPDATE sales SET qat_type_id = ? WHERE qat_type_id = ?")->execute([$safe_id, $t['id']]);
        $pdo->prepare("UPDATE purchases SET qat_type_id = ? WHERE qat_type_id = ?")->execute([$safe_id, $t['id']]);

        // Delete
        try {
            $pdo->prepare("DELETE FROM qat_types WHERE id = ?")->execute([$t['id']]);
            echo "Successfully deleted {$t['name']}\n";
        } catch (Exception $e) {
            echo "Final attempt to delete {$t['name']} failed: " . $e->getMessage() . "\n";
        }
    }
}
