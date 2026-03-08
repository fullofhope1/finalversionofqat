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

$stmt = $pdo->prepare("SELECT id FROM qat_types WHERE name = 'جمام نقوة' LIMIT 1");
$stmt->execute();
$safe_id = $stmt->fetchColumn();

if (!$safe_id) die("Critical Error: 'جمام نقوة' not found.\n");

$stmt = $pdo->query("SELECT id, name FROM qat_types");
$all = $stmt->fetchAll();

foreach ($all as $t) {
    if (!in_array($t['name'], $target_names)) {
        echo "Processing: {$t['name']} (ID {$t['id']})\n";

        // Re-map ALL known references
        $pdo->prepare("UPDATE sales SET qat_type_id = ? WHERE qat_type_id = ?")->execute([$safe_id, $t['id']]);
        $pdo->prepare("UPDATE purchases SET qat_type_id = ? WHERE qat_type_id = ?")->execute([$safe_id, $t['id']]);
        $pdo->prepare("UPDATE leftovers SET qat_type_id = ? WHERE qat_type_id = ?")->execute([$safe_id, $t['id']]);

        // Delete
        try {
            $pdo->prepare("DELETE FROM qat_types WHERE id = ?")->execute([$t['id']]);
            echo "Deleted {$t['name']}\n";
        } catch (Exception $e) {
            echo "Failed to delete {$t['name']}: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nFinal List:\n";
$stmt = $pdo->query("SELECT name FROM qat_types");
while ($row = $stmt->fetch()) {
    echo "- " . $row['name'] . "\n";
}
