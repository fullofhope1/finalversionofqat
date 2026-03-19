<?php
require_once 'config/db.php';

// Clear and reset for absolute certainty
$pdo->exec("DELETE FROM users WHERE username IN ('admin', 'superadmin')");

$users = [
    ['u' => 'admin', 'p' => password_hash('admin123', PASSWORD_DEFAULT), 'r' => 'admin'],
    ['u' => 'superadmin', 'p' => password_hash('super123', PASSWORD_DEFAULT), 'r' => 'super_admin']
];

foreach ($users as $user) {
    $stmt = $pdo->prepare("INSERT INTO users (username, display_name, password, role, sub_role) VALUES (?, ?, ?, ?, 'full')");
    $stmt->execute([$user['u'], $user['u'], $user['p'], $user['r']]);
    echo "Inserted {$user['u']}\n";
}

$stmt = $pdo->query("SELECT username, role FROM users");
while ($row = $stmt->fetch()) {
    echo "USER: " . $row['username'] . " | ROLE: " . $row['role'] . "\n";
}
