<?php
// includes/classes/SaleService.php

class SaleService extends BaseService
{
    private $saleRepo;
    private $purchaseRepo;
    private $customerRepo;
    private $leftoverRepo;
    private $unitService;

    public function __construct(
        SaleRepository $saleRepo,
        PurchaseRepository $purchaseRepo,
        CustomerRepository $customerRepo,
        LeftoverRepository $leftoverRepo,
        UnitSalesService $unitService
    ) {
        $this->saleRepo = $saleRepo;
        $this->purchaseRepo = $purchaseRepo;
        $this->customerRepo = $customerRepo;
        $this->leftoverRepo = $leftoverRepo;
        $this->unitService = $unitService;
    }

    public function getTodaysStock($date)
    {
        $stock = $this->purchaseRepo->getFreshStockByDate($date);
        $salesList = $this->saleRepo->getSalesMap($date);

        $salesMap = [];
        foreach ($salesList as $s) {
            $salesMap[$s['purchase_id']] = [
                'sold_kg' => (float)$s['sold_kg'],
                'sold_units' => (int)$s['sold_units']
            ];
        }

        foreach ($stock as &$item) {
            $soldData = $salesMap[$item['id']] ?? ['sold_kg' => 0, 'sold_units' => 0];
            $item['remaining_kg'] = round((float)($item['quantity_kg'] ?? 0) - $soldData['sold_kg'], 3);
            $item['remaining_units'] = (int)($item['received_units'] ?? 0) - $soldData['sold_units'];
        }
        return $stock;
    }

    public function getAvailableLeftoverStock()
    {
        $leftovers = $this->leftoverRepo->getTransferredLeftovers();

        $leftoverStocks = [];

        foreach ($leftovers as $l) {
            $soldKg = (float)$this->saleRepo->getSoldKgByLeftoverId($l['id']);
            $soldUnits = (int)$this->saleRepo->getSoldUnitsByLeftoverId($l['id']);

            $remKg = (float)$l['weight_kg'] - $soldKg;
            $remUnits = (int)$l['quantity_units'] - $soldUnits;

            if ($remKg > 0.001 || $remUnits > 0) {
                $l['remaining_kg'] = round($remKg, 3);
                $l['remaining_units'] = $remUnits;
                $l['provider_name'] = $l['provider_name'] ?: 'بقايا عامة (General)';
                $l['type'] = 'leftover';
                $leftoverStocks[] = $l;
            }
        }

        return $leftoverStocks;
    }

    public function processSale(array $data)
    {
        $this->saleRepo->beginTransaction();
        try {
            $data['unit_type'] = $data['unit_type'] ?? 'weight';
            $data['quantity_units'] = (int)($data['quantity_units'] ?? 0);
            $data['due_date'] = $data['due_date'] ?? $data['sale_date']; // Default to today

            if ($data['unit_type'] === 'weight') {
                $weightKg = (float)($data['weight_grams'] ?? 0) / 1000;
                $data['weight_kg'] = $weightKg;

                // 1. Inventory Check (Weight)
                if (!empty($data['purchase_id'])) {
                    $totalPurchased = (float)$this->purchaseRepo->getStockQuantity($data['purchase_id'], true);
                    $totalSold = (float)$this->saleRepo->getSoldKgByPurchaseId($data['purchase_id']);
                    $available = round($totalPurchased - $totalSold, 3);

                    if ($weightKg > $available) {
                        throw new Exception("InventoryExceeded|{$available}|{$weightKg}");
                    }
                } elseif (!empty($data['leftover_id'])) {
                    $totalLeftover = (float)$this->leftoverRepo->getWeight($data['leftover_id'], true);
                    $totalSold = (float)$this->saleRepo->getSoldKgByLeftoverId($data['leftover_id']);
                    $available = round($totalLeftover - $totalSold, 3);

                    if ($weightKg > $available) {
                        throw new Exception("LeftoverExceeded|{$available}|{$weightKg}");
                    }
                }
            } else {
                // Unit-based sale (Qabdah / Qartas)
                $sourceType = !empty($data['purchase_id']) ? 'purchase' : 'leftover';
                $sourceId = !empty($data['purchase_id']) ? $data['purchase_id'] : $data['leftover_id'];

                $this->unitService->validateInventory($sourceType, $sourceId, $data['quantity_units']);
                $this->unitService->prepareSaleData($data);
            }

            // 2. Credit Limit Check
            if ($data['payment_method'] === 'Debt' && !empty($data['customer_id'])) {
                $cust = $this->customerRepo->getById($data['customer_id']);
                if ($cust) {
                    $newDebt = (float)$cust['total_debt'] + (float)$data['price'];
                    if ($cust['debt_limit'] !== null && $newDebt > $cust['debt_limit']) {
                        throw new Exception("CreditLimitExceeded|{$cust['debt_limit']}|{$cust['total_debt']}");
                    }
                }
            }

            // 3. Create Sale
            $saleId = $this->saleRepo->create($data);

            // 4. Update Customer Debt
            if ($data['payment_method'] === 'Debt' && !empty($data['customer_id'])) {
                $this->customerRepo->incrementDebt($data['customer_id'], $data['price']);
            }

            $this->saleRepo->commit();
            return $saleId;
        } catch (Exception $e) {
            $this->saleRepo->rollBack();
            throw $e;
        }
    }
}
