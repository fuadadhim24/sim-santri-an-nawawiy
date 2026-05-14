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
        
        // Find active fee masters that are MONTHLY or YEARLY
        $feeMasters = \App\Models\FeeMaster::where('is_active', true)
            ->whereIn('recurrence_type', ['MONTHLY', 'YEARLY'])
            ->get();

        $totalGenerated = 0;

        foreach ($feeMasters as $feeMaster) {
            // Handle edge case: if billing_day is 31 but month has 30 days, trigger it on the 30th.
            $targetDay = $feeMaster->billing_day;
            if ($targetDay > $lastDayOfMonth) {
                $targetDay = $lastDayOfMonth;
            }

            // Only run if today matches the billing day
            if ($todayDay !== $targetDay) {
                continue;
            }

            // For YEARLY, we also need to check if it's the correct month to generate
            if ($feeMaster->recurrence_type === 'YEARLY' && $feeMaster->start_date) {
                $startMonth = \Carbon\Carbon::parse($feeMaster->start_date)->month;
                if ($currentMonth !== $startMonth) {
                    continue; // Not the right month for yearly billing
                }
            }

            // Get students matching the criteria
            $query = \App\Models\Student::where('is_active', true)->where('status', 'diterima');
            
            if ($feeMaster->unit_target) {
                $query->where('unit_code', $feeMaster->unit_target);
            }
            if ($feeMaster->residence_target) {
                $query->where('residence_status', $feeMaster->residence_target);
            }

            $students = $query->get();
            $generatedForFee = 0;

            foreach ($students as $student) {
                $billingQuery = \App\Models\Billing::where('student_id', $student->id)
                    ->where('fee_master_id', $feeMaster->id);

                if ($feeMaster->recurrence_type === 'MONTHLY') {
                    $billingQuery->whereMonth('created_at', $currentMonth)
                                 ->whereYear('created_at', $currentYear);
                } elseif ($feeMaster->recurrence_type === 'YEARLY') {
                    $billingQuery->whereYear('created_at', $currentYear);
                }

                if (!$billingQuery->exists()) {
                    $discountAmount = 0;
                    if ($student->special_status !== 'UMUM') {
                        $discount = \App\Models\Discount::where('fee_master_id', $feeMaster->id)
                            ->where('target_status', $student->special_status)
                            ->first();
                        if ($discount) {
                            $discountAmount = $discount->discount_amount;
                        }
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
