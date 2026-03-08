<?php
require_once 'config/db.php';

// 1. Ensure table has all columns
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS display_name VARCHAR(100) AFTER username");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(20) AFTER display_name");
    echo "Table columns updated.\n";
} catch (Exception $e) {
    echo "Error updating table: " . $e->getMessage() . "\n";
}

// 2. Insert/Update superadmin
$username = 'superadmin';
$password = 'SuperSecret123!';
$hash = password_hash($password, PASSWORD_DEFAULT);
$role = 'super_admin';
$sub_role = 'full';

try {
    $stmt = $pdo->prepare("INSERT INTO users (username, display_name, password, role, sub_role) 
                           VALUES (?, ?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE 
                           password = VALUES(password), 
                           role = VALUES(role), 
                           sub_role = VALUES(sub_role)");
    $stmt->execute([$username, 'Super Admin', $hash, $role, $sub_role]);
    echo "Super Admin account created/updated successfully.\n";
    echo "Username: $username\n";
    echo "Password: $password\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
