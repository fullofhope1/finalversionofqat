<?php
// includes/classes/UnitSalesService.php

class UnitSalesService extends BaseService
{
    private $purchaseRepo;
    private $leftoverRepo;
    private $saleRepo;

    public function __construct(
        PurchaseRepository $purchaseRepo,
        LeftoverRepository $leftoverRepo,
        SaleRepository $saleRepo
    ) {
        $this->purchaseRepo = $purchaseRepo;
        $this->leftoverRepo = $leftoverRepo;
        $this->saleRepo = $saleRepo;
    }

    /**
     * Checks if requested units are available in inventory (Purchase or Leftover)
     */
    public function validateInventory($sourceType, $sourceId, $requestedUnits)
    {
        if ($sourceType === 'purchase') {
            $total = $this->purchaseRepo->getStockUnits($sourceId, true);
            $sold = $this->saleRepo->getSoldUnitsByPurchaseId($sourceId);
        } else {
            $total = $this->leftoverRepo->getUnits($sourceId, true);
            $sold = $this->saleRepo->getSoldUnitsByLeftoverId($sourceId);
        }

        $available = (int)$total - (int)$sold;
        if ($requestedUnits > $available) {
            throw new Exception("UnitInventoryExceeded|{$available}|{$requestedUnits}");
        }
        return true;
    }

    /**
     * Helper to prepare unit-based sale data
     */
    public function prepareSaleData(array &$data)
    {
        // Technique-specific logic can go here (e.g. forced weight=0 for unit sales)
        if ($data['unit_type'] !== 'weight') {
            $data['weight_grams'] = 0;
            $data['weight_kg'] = 0;
        }
    }
}
