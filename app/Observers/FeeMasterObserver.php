<?php

namespace App\Observers;

use App\Jobs\GenerateOnceBillingsJob;
use App\Models\FeeMaster;

class FeeMasterObserver
{
    public function created(FeeMaster $feeMaster): void
    {
        if ($feeMaster->category && $feeMaster->category->billing_interval === 'ONCE') {
            GenerateOnceBillingsJob::dispatch($feeMaster);
        }
    }
}
