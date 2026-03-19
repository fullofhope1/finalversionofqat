<?php
require 'config/db.php';
require_once 'includes/Autoloader.php';

header('Content-Type: text/plain; charset=utf-8');

echo "--- Users List --- \n";
$stmt = $pdo->query("SELECT id, username, role FROM users");
print_r($stmt->fetchAll());
