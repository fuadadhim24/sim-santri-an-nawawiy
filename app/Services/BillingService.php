<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Discount;
use App\Models\FeeMaster;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingService
{
    /**
     * Generate a bill for a student based on a specific fee category and item name.
     * This handles finding the correct fee for the student's unit/residence and applying discounts.
     */
    public function generateBill(Student $student, int $feeCategoryId, string $title, ?string $feeItemName = null)
    {
        $query = FeeMaster::where('fee_category_id', $feeCategoryId)
            ->where(function ($q) use ($student) {
                $q->where('unit_target', $student->unit_code)
                  ->orWhereNull('unit_target');
            })
            ->where(function ($q) use ($student) {
                $q->where('residence_target', $student->residence_status)
                  ->orWhereNull('residence_target');
            });

        if ($feeItemName) {
            $query->where('item_name', $feeItemName);
        }

        $fees = $query->get();

        if ($fees->isEmpty()) {
            return null;
        }

        $totalOriginalAmount = 0;
        $totalDiscount = 0;
        $discounts = collect();

        if ($student->special_status !== 'UMUM') {
            $feeIds = $fees->pluck('id');
            $discounts = Discount::whereIn('fee_master_id', $feeIds)
                ->where('target_status', $student->special_status)
                ->get()
                ->keyBy('fee_master_id');
        }

        foreach ($fees as $fee) {
            $amount = $fee->amount;
            $discountAmount = 0;

            if ($student->special_status !== 'UMUM') {
                $discount = $discounts[$fee->id] ?? null;
                if ($discount) {
                    $discountAmount = $discount->discount_amount;
                }
            }

            $totalOriginalAmount += $amount;
            $totalDiscount += $discountAmount;
        }

        if ($totalDiscount > $totalOriginalAmount) {
            $totalDiscount = $totalOriginalAmount;
        }

        $finalAmount = $totalOriginalAmount - $totalDiscount;

        $firstFee = $fees->first();

        return Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $firstFee?->id,
            'title' => $title,
            'original_amount' => $totalOriginalAmount,
            'discount_applied' => $totalDiscount,
            'final_amount' => $finalAmount,
            'status' => 'UNPAID',
        ]);
    }

    /**
     * Eager load discounts for a collection of fees based on student's special status.
     */
    private function loadDiscountsForFees($fees, Student $student)
    {
        if ($student->special_status === 'UMUM' || $fees->isEmpty()) {
            return collect();
        }

        $feeIds = $fees->pluck('id');
        return Discount::whereIn('fee_master_id', $feeIds)
            ->where('target_status', $student->special_status)
            ->get()
            ->keyBy('fee_master_id');
    }

    /**
     * Helper to create a billing record from a FeeMaster item.
     */
    private function createBillFromFee(Student $student, FeeMaster $fee, string $title, $discounts = null)
    {
        $amount = $fee->amount;
        $discountAmount = 0;

        if ($student->special_status !== 'UMUM') {
            if ($discounts && isset($discounts[$fee->id])) {
                $discountAmount = $discounts[$fee->id]->discount_amount;
            } elseif ($discounts === null) {
                $discount = Discount::where('fee_master_id', $fee->id)
                    ->where('target_status', $student->special_status)
                    ->first();

                if ($discount) {
                    $discountAmount = $discount->discount_amount;
                }
            }
        }

        $finalAmount = max(0, $amount - $discountAmount);

        return Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $fee->id,
            'title' => $title,
            'original_amount' => $amount,
            'discount_applied' => $discountAmount,
            'final_amount' => $finalAmount,
            'status' => 'UNPAID',
        ]);
    }

    /**
     * Recalculate unpaid billings for a specific FeeMaster.
     * Called when discount is added/updated/deleted or when FeeMaster amount changes.
     */
    public function recalculateBillingsForFeeMaster(FeeMaster $feeMaster): int
    {
        $count = 0;

        DB::transaction(function () use ($feeMaster, &$count) {
            $billings = Billing::where('status', 'UNPAID')
                ->where('fee_master_id', $feeMaster->id)
                ->with('student')
                ->get();

            foreach ($billings as $billing) {
                $this->recalculateBilling($billing, $feeMaster);
                $count++;
            }
        });

        return $count;
    }

    /**
     * Recalculate a single billing based on current FeeMaster amount and discounts.
     */
    public function recalculateBilling(Billing $billing, ?FeeMaster $feeMaster = null): void
    {
        if ($billing->status !== 'UNPAID') {
            return;
        }

        $billing->loadMissing('student');
        $student = $billing->student;

        if (!$student) {
            return;
        }

        if (!$feeMaster) {
            $feeMaster = $billing->feeMaster ?? $this->findFeeMasterForBilling($billing, $student);
        }

        if (!$feeMaster) {
            Log::warning('recalculateBilling: Could not find FeeMaster for billing', [
                'billing_id' => $billing->id,
                'billing_title' => $billing->title,
                'student_id' => $student->id,
            ]);
            return;
        }

        $totalAmount = $feeMaster->amount;
        $totalDiscount = 0;

        if ($student->special_status !== 'UMUM') {
            $discount = Discount::where('fee_master_id', $feeMaster->id)
                ->where('target_status', $student->special_status)
                ->first();

            if ($discount) {
                $totalDiscount = $discount->discount_amount;
            }
        }

        $finalAmount = max(0, $totalAmount - $totalDiscount);

        $updateData = [
            'original_amount' => $totalAmount,
            'discount_applied' => $totalDiscount,
            'final_amount' => $finalAmount,
        ];

        if ($billing->fee_master_id === null) {
            Log::info('Backfilling fee_master_id for billing', [
                'billing_id' => $billing->id,
                'fee_master_id' => $feeMaster->id,
            ]);
            $updateData['fee_master_id'] = $feeMaster->id;
        }

        $billing->update($updateData);
    }

    /**
     * Find the FeeMaster that corresponds to a billing based on title matching.
     */
    private function findFeeMasterForBilling(Billing $billing, Student $student): ?FeeMaster
    {
        $baseTitle = $this->extractBaseTitle($billing->title);

        return FeeMaster::where('item_name', $baseTitle)
            ->where(function ($q) use ($student) {
                $q->where('unit_target', $student->unit_code)->orWhereNull('unit_target');
            })
            ->where(function ($q) use ($student) {
                $q->where('residence_target', $student->residence_status)->orWhereNull('residence_target');
            })
            ->first();
    }

    /**
     * Extract the base title from a billing title by removing year and month suffixes.
     */
    private function extractBaseTitle(string $title): string
    {
        $baseTitle = preg_replace('/[-_\s]+\d{4}$/', '', $title);
        $baseTitle = preg_replace('/[-_\s]+(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)[-_\s]+\d{4}$/i', '', $baseTitle);

        return trim($baseTitle);
    }

    /**
     * Recalculate all unpaid billings for a student.
     * Called when student's special_status changes.
     */
    public function recalculateStudentBillings(Student $student): int
    {
        $count = 0;

        DB::transaction(function () use ($student, &$count) {
            $billings = Billing::where('student_id', $student->id)
                ->where('status', 'UNPAID')
                ->with('feeMaster')
                ->get();

            foreach ($billings as $billing) {
                $this->recalculateBilling($billing);
                $count++;
            }
        });

        return $count;
    }

    public function generateOnceBillsForSelectedFees(Student $student, array $feeMasterIds): int
    {
        $count = 0;

        DB::transaction(function () use ($student, $feeMasterIds, &$count) {
            $fees = FeeMaster::whereIn('id', $feeMasterIds)->get();
            $discounts = $this->loadDiscountsForFees($fees, $student);

            foreach ($fees as $fee) {
                $this->createBillFromFee($student, $fee, $fee->item_name, $discounts);
                $count++;
            }
        });

        Log::info('Generated once bills for new student', [
            'student_id' => $student->id,
            'fee_master_ids' => $feeMasterIds,
            'count' => $count,
        ]);

        return $count;
    }
}
