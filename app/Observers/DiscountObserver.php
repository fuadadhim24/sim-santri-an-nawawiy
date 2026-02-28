<?php

namespace App\Observers;

use App\Models\Discount;
use App\Services\BillingService;
use Illuminate\Support\Facades\Log;

class DiscountObserver
{
    protected BillingService $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    public function created(Discount $discount): void
    {
        $this->recalculateBillings($discount, 'created');
    }

    public function updated(Discount $discount): void
    {
        if ($discount->wasChanged(['discount_amount', 'target_status'])) {
            $this->recalculateBillings($discount, 'updated');
        }
    }

    public function deleted(Discount $discount): void
    {
        $this->recalculateBillings($discount, 'deleted');
    }

    protected function recalculateBillings(Discount $discount, string $action): void
    {
        $feeMaster = $discount->feeMaster;

        if (!$feeMaster) {
            return;
        }

        $count = $this->billingService->recalculateBillingsForFeeMaster($feeMaster);

        Log::info("Discount {$action}: recalculated billings", [
            'discount_id' => $discount->id,
            'fee_master_id' => $feeMaster->id,
            'target_status' => $discount->target_status,
            'billings_updated' => $count,
        ]);
    }
}
