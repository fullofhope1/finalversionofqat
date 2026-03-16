<?php
require_once '../config/db.php';
require_once '../includes/Autoloader.php';
require_once '../includes/require_auth.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? 0;

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'ID مطلوب']);
    exit;
}

try {
    $repo = new ProviderRepository($pdo);
    $service = new ProviderService($repo);
    $service->removeProvider($id);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
