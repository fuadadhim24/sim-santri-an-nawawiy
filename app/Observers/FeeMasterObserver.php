<?php

namespace App\Observers;

use App\Models\FeeMaster;
use App\Services\BillingService;
use Illuminate\Support\Facades\Log;

class FeeMasterObserver
{
    protected BillingService $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    public function updated(FeeMaster $feeMaster): void
    {
        // Billing changes are not retroactive (tidak berlaku surut)
    }
}
