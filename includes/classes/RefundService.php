<?php
// includes/classes/RefundService.php

class RefundService extends BaseService
{
    protected $refundRepo;
    protected $customerRepo;

    public function __construct(RefundRepository $refundRepo, CustomerRepository $customerRepo)
    {
        $this->refundRepo = $refundRepo;
        $this->customerRepo = $customerRepo;
    }

    public function getRefundDashboardData()
    {
        return [
            'recent_refunds' => $this->refundRepo->getRecentRefunds(),
            'customers' => $this->customerRepo->getAllActive()
        ];
    }

    public function processRefund($data, $userId = null)
    {
        $this->refundRepo->beginTransaction();
        try {
            // 1. Create refund record
            $data['created_by'] = $userId;
            $this->refundRepo->create($data);

            // 2. If it's a debt refund, decrement customer debt AND update sales records for report consistency
            if ($data['refund_type'] === 'Debt') {
                $customerId = $data['customer_id'];
                $amount = (float)$data['amount'];

                // A. Direct customer balance update
                $this->customerRepo->decrementDebt($customerId, $amount);

                // B. Distribute refund across unpaid sales (for report consistency)
                // We fetch unpaid sales for this customer, ordered by newest first
                $unpaidSales = $this->refundRepo->getUnpaidSalesWithBalance($customerId);

                $remainingToApply = $amount;
                foreach ($unpaidSales as $sale) {
                    if ($remainingToApply <= 0) {
                        break;
                    }

                    $canApply = min($remainingToApply, (float)$sale['remaining_debt']);
                    if ($canApply > 0) {
                        $this->refundRepo->applyRefundToSale($sale['id'], $canApply);
                        $remainingToApply -= $canApply;
                    }
                }
            }

            $this->refundRepo->commit();
            return true;
        } catch (Exception $e) {
            $this->refundRepo->rollBack();
            throw $e;
        }
    }
}
