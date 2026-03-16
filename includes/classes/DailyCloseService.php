<?php
// includes/classes/DailyCloseService.php

class DailyCloseService extends BaseService
{
    private $repository;

    public function __construct(DailyCloseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function closeDay($currentDate)
    {
        $tomorrow = date('Y-m-d', strtotime($currentDate . ' +1 day'));

        try {
            $this->repository->beginTransaction();

            // --- STEP 1: CLEAN OLD LEFTOVERS (THE EMPTY BUCKET) ---
            // Identify EVERYTHING in 'Momsi' or active 'Leftover' state and trash it.

            // 1a. Handle manual/auto leftovers in leftovers table
            $staleLeftovers = $this->repository->getActiveManualLeftovers();
            foreach ($staleLeftovers as $l) {
                $this->repository->trashLeftover($l['id'], $currentDate);
            }

            // 1b. Handle Momsi purchases in purchases table
            $stalePurchases = $this->repository->getActiveMomsiStock($currentDate);
            foreach ($stalePurchases as $p) {
                $this->repository->trashMomsiPurchase($p['id'], $currentDate);
            }

            // --- STEP 2: MOVE TODAY'S SALES (SURPLUS) ---
            // Triple-Action Sweep: Identify today's Fresh stock and move remaining quantity/units to tomorrow.
            $dayFresh = $this->repository->getDayFreshStock($currentDate);
            foreach ($dayFresh as $p) {
                $stats = $this->repository->getSoldAndManagedForPurchase($p['id']);

                $surplusKg = (float)$p['quantity_kg'] - (float)$stats['sold_kg'] - (float)$stats['managed_kg'];
                $surplusUnits = (int)($p['received_units'] ?? 0) - (int)$stats['sold_units'] - (int)$stats['managed_units'];

                if ($surplusKg > 0.001 || $surplusUnits > 0) {
                    $this->repository->moveStockToTomorrow($p['id'], $surplusKg, $surplusUnits, $currentDate, $tomorrow);
                } else {
                    $this->repository->closePurchase($p['id']);
                }
            }

            // --- STEP 3: MIGRATE DAILY DEBTS ---
            $this->repository->migrateDailyDebts($currentDate, $tomorrow);

            $this->repository->commit();
            return true;
        } catch (Exception $e) {
            if ($this->repository->inTransaction()) {
                $this->repository->rollBack();
            }
            throw $e;
        }
    }
}
