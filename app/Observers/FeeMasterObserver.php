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
        if ($feeMaster->wasChanged(['amount', 'unit_target', 'residence_target'])) {
            $count = $this->billingService->recalculateBillingsForFeeMaster($feeMaster);

            $changes = [];
            if ($feeMaster->wasChanged('amount')) {
                $changes['amount'] = [
                    'old' => $feeMaster->getOriginal('amount'),
                    'new' => $feeMaster->amount,
                ];
            }
            if ($feeMaster->wasChanged('unit_target')) {
                $changes['unit_target'] = [
                    'old' => $feeMaster->getOriginal('unit_target'),
                    'new' => $feeMaster->unit_target,
                ];
            }
            if ($feeMaster->wasChanged('residence_target')) {
                $changes['residence_target'] = [
                    'old' => $feeMaster->getOriginal('residence_target'),
                    'new' => $feeMaster->residence_target,
                ];
            }

            Log::info('FeeMaster changed: recalculated billings', [
                'fee_master_id' => $feeMaster->id,
                'item_name' => $feeMaster->item_name,
                'changes' => $changes,
                'billings_updated' => $count,
            ]);
        }
    }
}
