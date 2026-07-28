<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateRecurringBillings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:generate-recurring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate recurring monthly and yearly billings for students';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting recurring billings generation...');

        $todayDay = now()->day;
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $lastDayOfMonth = now()->daysInMonth;
        
        $feeMasters = \App\Models\FeeMaster::with('category')
            ->activeWithinDates()
            ->whereHas('category', function ($query) {
                $query->where('is_active', true);
            })
            ->whereIn('recurrence_type', ['MONTHLY', 'EVERY_6_MONTHS', 'YEARLY'])
            ->get();

        $totalGenerated = 0;

        foreach ($feeMasters as $feeMaster) {
            $targetDay = $feeMaster->billing_day;
            if ($targetDay > $lastDayOfMonth) {
                $targetDay = $lastDayOfMonth;
            }

            if ($todayDay !== $targetDay) {
                continue;
            }

            if ($feeMaster->recurrence_type === 'EVERY_6_MONTHS' && $feeMaster->start_date) {
                $startDate = \Carbon\Carbon::parse($feeMaster->start_date)->startOfMonth();
                $currentDate = now()->startOfMonth();
                $diffInMonths = $startDate->diffInMonths($currentDate);
                if ($diffInMonths % 6 !== 0) {
                    continue; 
                }
            }

            if ($feeMaster->recurrence_type === 'YEARLY' && $feeMaster->start_date) {
                $startMonth = \Carbon\Carbon::parse($feeMaster->start_date)->month;
                if ($currentMonth !== $startMonth) {
                    continue; 
                }
            }

            $query = \App\Models\Student::where('is_active', true)->where('status', 'diterima');
            
            // Apply category-level targets
            if ($feeMaster->category->unit_target) {
                $query->where('unit_code', $feeMaster->category->unit_target);
            }
            if ($feeMaster->category->domicile_target) {
                $query->where('residence_status', $feeMaster->category->domicile_target);
            }

            // Apply item-level targets
            if ($feeMaster->unit_target) {
                $query->where('unit_code', $feeMaster->unit_target);
            }
            if ($feeMaster->residence_target) {
                $query->where('residence_status', $feeMaster->residence_target);
            }
            if ($feeMaster->class_level_target_id) {
                $query->where('class_level_id', $feeMaster->class_level_target_id);
            }

            $students = $query->with('approvedSpecialStatuses')->get();
            $generatedForFee = 0;

            foreach ($students as $student) {
                $billingQuery = \App\Models\Billing::where('student_id', $student->id)
                    ->where('fee_master_id', $feeMaster->id);

                if ($feeMaster->recurrence_type === 'MONTHLY') {
                    $billingQuery->whereMonth('created_at', $currentMonth)
                                 ->whereYear('created_at', $currentYear);
                } elseif ($feeMaster->recurrence_type === 'EVERY_6_MONTHS') {
                    $billingQuery->where('created_at', '>=', now()->subMonths(5)->startOfMonth());
                } elseif ($feeMaster->recurrence_type === 'YEARLY') {
                    $billingQuery->whereYear('created_at', $currentYear);
                }

                if (!$billingQuery->exists()) {
                    $discountAmount = 0;
                    if ($student->hasAnyApprovedSpecialStatus()) {
                        $statusCodes = $student->getApprovedSpecialStatusCodes();
                        $discountAmount = \App\Models\Discount::where('fee_master_id', $feeMaster->id)
                            ->whereIn('target_status', $statusCodes)
                            ->sum('discount_amount');
                        // Cap diskon maksimum 100% dari tagihan
                        $discountAmount = min($discountAmount, $feeMaster->amount);
                    }

                    $finalAmount = max(0, $feeMaster->amount - $discountAmount);
                    $dueDate = now()->addDays($feeMaster->due_days ?? 14)->format('Y-m-d');

                    \App\Models\Billing::create([
                        'student_id' => $student->id,
                        'fee_master_id' => $feeMaster->id,
                        'title' => $feeMaster->item_name,
                        'original_amount' => $feeMaster->amount,
                        'discount_applied' => $discountAmount,
                        'final_amount' => $finalAmount,
                        'status' => 'UNPAID',
                        'due_date' => $dueDate,
                        'visible_to_wali' => true,
                        'version' => 1,
                    ]);

                    $generatedForFee++;
                    $totalGenerated++;
                }
            }

            if ($generatedForFee > 0) {
                $this->info("Generated $generatedForFee bills for Fee Master: {$feeMaster->item_name}");
            }
        }

        $this->info("Recurring billings generation completed. Total generated: $totalGenerated");
    }
}
