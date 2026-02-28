<?php

namespace App\Jobs;

use App\Models\Billing;
use App\Models\Discount;
use App\Models\FeeMaster;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateOnceBillingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public FeeMaster $feeMaster;

    public function __construct(FeeMaster $feeMaster)
    {
        $this->feeMaster = $feeMaster;
    }

    public function handle(): void
    {
        $feeMaster = $this->feeMaster;
        $category = $feeMaster->category;

        if (!$category || $category->billing_interval !== 'ONCE') {
            return;
        }

        $students = Student::query()
            ->where('is_active', true)
            ->where('residence_status', '!=', 'NGAJI_ONLY')
            ->when($feeMaster->unit_target !== null, function ($q) use ($feeMaster) {
                $q->where('unit_code', $feeMaster->unit_target);
            })
            ->when($feeMaster->residence_target !== null, function ($q) use ($feeMaster) {
                $q->where('residence_status', $feeMaster->residence_target);
            })
            ->get();

        $discounts = Discount::where('fee_master_id', $feeMaster->id)
            ->whereIn('target_status', $students->pluck('special_status')->unique()->filter())
            ->get()
            ->keyBy('target_status');

        $generatedCount = 0;

        DB::transaction(function () use ($students, $feeMaster, &$generatedCount, $discounts) {
            foreach ($students as $student) {
                $title = $feeMaster->item_name;
                $amount = $feeMaster->amount;
                $discountAmount = 0;

                if ($student->special_status !== 'UMUM' && isset($discounts[$student->special_status])) {
                    $discountAmount = $discounts[$student->special_status]->discount_amount;
                }

                $finalAmount = max(0, $amount - $discountAmount);

                $billing = Billing::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'title' => $title,
                    ],
                    [
                        'original_amount' => $amount,
                        'discount_applied' => $discountAmount,
                        'final_amount' => $finalAmount,
                        'status' => 'UNPAID',
                    ]
                );

                if ($billing->wasRecentlyCreated) {
                    $generatedCount++;
                }
            }
        });

        Log::info("GenerateOnceBillingsJob completed", [
            'fee_master_id' => $feeMaster->id,
            'item_name' => $feeMaster->item_name,
            'students_processed' => $students->count(),
            'billings_generated' => $generatedCount,
        ]);
    }
}
