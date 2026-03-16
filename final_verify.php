<?php
require_once 'config/db.php';
require_once 'includes/Autoloader.php';

$purchaseRepo = new PurchaseRepository($pdo);
$saleRepo = new SaleRepository($pdo);
$customerRepo = new CustomerRepository($pdo);
$leftoverRepo = new LeftoverRepository($pdo);
$unitSalesService = new UnitSalesService($purchaseRepo, $leftoverRepo, $saleRepo);
$saleService = new SaleService($saleRepo, $purchaseRepo, $customerRepo, $leftoverRepo, $unitSalesService);

$stock = $saleService->getTodaysStock(date('Y-m-d'));
foreach ($stock as $s) {
    if ($s['id'] == 341 || $s['id'] == 384) {
        echo "ID: " . $s['id'] . " | Provider: " . $s['provider_name'] . " | Remaining: " . ($s['remaining_units'] ?? $s['remaining_kg']) . "\n";
    }
}
