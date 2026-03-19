<?php
require_once 'config/db.php';
$stmt = $pdo->query("SELECT id, username, role FROM users");
$users = $stmt->fetchAll();
if (empty($users)) {
    echo "No users found in database.\n";
} else {
    foreach ($users as $u) {
        echo "ID: {$u['id']} | User: {$u['username']} | Role: {$u['role']}\n";
    }
}
