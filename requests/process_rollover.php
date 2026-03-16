<?php
// requests/process_rollover.php
// SECURITY: Changed from GET to POST-only, added auth
require '../config/db.php';
require '../includes/Autoloader.php';
require_once '../includes/require_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sale_id'])) {
    $sale_id = (int)$_POST['sale_id'];

    $debtRepo = new DebtRepository($pdo);
    $service = new DebtService($debtRepo);

    if ($service->rolloverSale($sale_id)) {
        header("Location: ../reports.php?success=RolloverDone");
        exit;
    }
}
header("Location: ../reports.php");
exit;
