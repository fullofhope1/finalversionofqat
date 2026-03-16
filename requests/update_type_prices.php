<?php
// requests/update_type_prices.php
require_once '../config/db.php';
require_once '../includes/Autoloader.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    try {
        $id = (int)$_POST['id'];
        $price_weight = (float)$_POST['price_weight'];
        $price_qabdah = (float)$_POST['price_qabdah'];
        $price_qartas = (float)$_POST['price_qartas'];

        $stmt = $pdo->prepare("UPDATE qat_types SET 
            price_weight = ?, 
            price_qabdah = ?, 
            price_qartas = ? 
            WHERE id = ?");

        $success = $stmt->execute([$price_weight, $price_qabdah, $price_qartas, $id]);

        echo json_encode(['success' => $success]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
