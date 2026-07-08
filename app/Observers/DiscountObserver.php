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
        // Not retroactive
    }

    public function updated(Discount $discount): void
    {
        // Not retroactive
    }

    public function deleted(Discount $discount): void
    {
        // Not retroactive
    }
}
