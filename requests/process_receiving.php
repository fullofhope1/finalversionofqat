<?php
// requests/process_receiving.php
require_once '../config/db.php';
require_once '../includes/Autoloader.php';
require_once '../includes/require_auth.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $purchaseRepo = new PurchaseRepository($pdo);
        $productRepo = new ProductRepository($pdo);
        $service = new PurchaseService($purchaseRepo, $productRepo);

        $receivedWeight = $_POST['received_weight_grams'] ?? 0;
        $receivedUnits = $_POST['received_units'] ?? 0;

        $service->receiveShipment($_POST['purchase_id'], $receivedWeight, $receivedUnits);

        header("Location: ../purchases.php?success=received");
        exit;
    } catch (Exception $e) {
        $errorMsg = urlencode($e->getMessage());
        header("Location: ../purchases.php?error=$errorMsg");
        exit;
    }
} else {
    header("Location: ../purchases.php");
    exit;
}
