<?php
require_once 'config/db.php';

// Target types mapping
$mapping = [
    'Jamam Naqwah' => 'جمام نقوة',
    'Jamam Kalif'  => 'جمام كالف',
    'Jamam Samin'  => 'جمام سمين',
    'Jamam Qasar'  => 'جمام قصار',
    'Sudur Naqwah' => 'صدور نقوة',
    'Sudur Adi'    => 'صدور عادي',
    'Qatal'        => 'قطل'
];

$target_names = array_values($mapping);

// 1. Ensure target types exist
foreach ($target_names as $name) {
    $stmt = $pdo->prepare("SELECT id FROM qat_types WHERE name = ?");
    $stmt->execute([$name]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO qat_types (name) VALUES (?)");
        $stmt->execute([$name]);
        echo "Inserted: $name\n";
    }
}

// Get final map of name -> id
$stmt = $pdo->query("SELECT id, name FROM qat_types");
$all_types = $stmt->fetchAll();
$name_to_id = [];
foreach ($all_types as $t) {
    $name_to_id[$t['name']] = $t['id'];
}

// 2. Re-map existing types in sales and purchases
foreach ($mapping as $old => $new) {
    if (isset($name_to_id[$old]) && isset($name_to_id[$new])) {
        $old_id = $name_to_id[$old];
        $new_id = $name_to_id[$new];

        if ($old_id != $new_id) {
            $pdo->prepare("UPDATE sales SET qat_type_id = ? WHERE qat_type_id = ?")->execute([$new_id, $old_id]);
            $pdo->prepare("UPDATE purchases SET qat_type_id = ? WHERE qat_type_id = ?")->execute([$new_id, $old_id]);
            echo "Re-mapped $old (ID $old_id) to $new (ID $new_id)\n";
        }
    }
}

// Special case: Unit Test mapping if it has data
// If 'Unit Test' has data, we'll map it to 'جمام نقوة' just to prevent deletion failure
// but the user's list is small, so maybe it's fine to just let it fail if I missed something.

// 3. Delete types not in the target list
$stmt = $pdo->query("SELECT id, name FROM qat_types");
$final_types = $stmt->fetchAll();
foreach ($final_types as $t) {
    if (!in_array($t['name'], $target_names)) {
        try {
            // First check if it's still used
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM sales WHERE qat_type_id = ?");
            $stmt_check->execute([$t['id']]);
            $s_count = $stmt_check->fetchColumn();

            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM purchases WHERE qat_type_id = ?");
            $stmt_check->execute([$t['id']]);
            $p_count = $stmt_check->fetchColumn();

            if ($s_count > 0 || $p_count > 0) {
                echo "Cannot delete {$t['name']} (ID {$t['id']}) - still has data ($s_count sales, $p_count purchases). Re-mapping to 'جمام نقوة'...\n";
                $pdo->prepare("UPDATE sales SET qat_type_id = ? WHERE qat_type_id = ?")->execute([$name_to_id['جمام نقوة'], $t['id']]);
                $pdo->prepare("UPDATE purchases SET qat_type_id = ? WHERE qat_type_id = ?")->execute([$name_to_id['جمام نقوة'], $t['id']]);
            }

            $pdo->prepare("DELETE FROM qat_types WHERE id = ?")->execute([$t['id']]);
            echo "Deleted: {$t['name']}\n";
        } catch (Exception $e) {
            echo "Failed to delete {$t['name']}: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nFinal Type List:\n";
$stmt = $pdo->query("SELECT * FROM qat_types");
while ($row = $stmt->fetch()) {
    echo "ID: {$row['id']} | Name: {$row['name']}\n";
}
