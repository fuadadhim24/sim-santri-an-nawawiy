<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Discount;
use App\Models\FeeMaster;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BillingService
{
    /**
     * Generate a bill for a student based on a specific fee category and item name.
     * This handles finding the correct fee for the student's unit/residence and applying discounts.
     */
    public function generateBill(Student $student, int $feeCategoryId, string $title, ?string $feeItemName = null)
    {
        // 1. Find the applicable Fee Master
        // We look for a fee that matches the category, and optionally the specific item name.
        // We also check if the fee is targeted towards the student's unit or residence.
        // If unit_target is null, it applies to all using 'where(function...)' logic?
        // Actually, let's keep it simple: Exact match or specific target matching.
        // FeeMaster logic:
        // - Category matches
        // - Unit target: Matches student's unit OR is null
        // - Residence target: Matches student's residence OR is null

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
            return null; // No applicable fee found
        }

        // For now, let's assume we sum up all applicable fees if there are multiple matches?
        // Or pick the most specific one?
        // Let's assume for a category like 'BULANAN' (SPP), there's ideally only one matching fee per student.
        // But if we have multiple items (e.g. SPP + Makan), we might want to generate separate bills or one combined bill.
        // With current schema, Billing has 'title' and 'final_amount', suggesting one bill per "Transaction".
        // Let's sum them up for this bill.

        $totalOriginalAmount = 0;
        $totalDiscount = 0;

        foreach ($fees as $fee) {
            $amount = $fee->amount;
            $discountAmount = 0;

            // 2. Check for Discounts
            // Discount links to a FeeMaster and a Target Status.
            // If student has a special_status (YATIM, ANAK_GURU), check for discounts.
            if ($student->special_status !== 'UMUM') {
                $discount = Discount::where('fee_master_id', $fee->id)
                    ->where('target_status', $student->special_status)
                    ->first();

                if ($discount) {
                    $discountAmount = $discount->discount_amount;
                }
            }

            $totalOriginalAmount += $amount;
            $totalDiscount += $discountAmount;
        }

        // Ensure we don't discount more than the amount (just in case)
        if ($totalDiscount > $totalOriginalAmount) {
            $totalDiscount = $totalOriginalAmount;
        }

        $finalAmount = $totalOriginalAmount - $totalDiscount;

        // 3. Create Billing Record
        return Billing::create([
            'student_id' => $student->id,
            'title' => $title,
            'original_amount' => $totalOriginalAmount,
            'discount_applied' => $totalDiscount,
            'final_amount' => $finalAmount,
            'status' => 'UNPAID', // Default
        ]);
    }

    /**
     * Generate bills that occur only once (e.g. registration).
     */
    public function generateOnceBills(Student $student)
    {
        $fees = FeeMaster::whereHas('category', function ($q) {
                $q->where('billing_interval', 'ONCE');
            })
            ->where(function ($q) use ($student) {
                $q->where('unit_target', $student->unit_code)->orWhereNull('unit_target');
            })
            ->where(function ($q) use ($student) {
                $q->where('residence_target', $student->residence_status)->orWhereNull('residence_target');
            })
            ->where(function ($q) {
                $now = now()->toDateString();
                $q->where('start_date', '<=', $now)->orWhereNull('start_date');
            })
            ->where(function ($q) {
                $now = now()->toDateString();
                $q->where('end_date', '>=', $now)->orWhereNull('end_date');
            })
            ->get();

        $count = 0;
        foreach ($fees as $fee) {
            $title = $fee->item_name;
            $exists = Billing::where('student_id', $student->id)->where('title', $title)->exists();
            if (!$exists) {
                $this->createBillFromFee($student, $fee, $title);
                $count++;
            }
        }
        return $count;
    }

    /**
     * Generate bills that occur once a year (e.g. re-registration).
     */
    public function generateYearlyBills(Student $student, $year)
    {
        $fees = FeeMaster::whereHas('category', function ($q) {
                $q->where('billing_interval', 'YEARLY');
            })
            ->where(function ($q) use ($student) {
                $q->where('unit_target', $student->unit_code)->orWhereNull('unit_target');
            })
            ->where(function ($q) use ($student) {
                $q->where('residence_target', $student->residence_status)->orWhereNull('residence_target');
            })
            ->where(function ($q) use ($year) {
                $targetDate = Carbon::create($year, 1, 1)->toDateString();
                $q->where('start_date', '<=', $targetDate)->orWhereNull('start_date');
            })
            ->where(function ($q) use ($year) {
                $targetDate = Carbon::create($year, 12, 31)->toDateString();
                $q->where('end_date', '>=', $targetDate)->orWhereNull('end_date');
            })
            ->get();

        $count = 0;
        foreach ($fees as $fee) {
            $title = $fee->item_name . " " . $year;
            $exists = Billing::where('student_id', $student->id)->where('title', $title)->exists();
            if (!$exists) {
                $this->createBillFromFee($student, $fee, $title);
                $count++;
            }
        }
        return $count;
    }

    /**
     * Generate monthly bills (e.g. SPP).
     */
    public function generateMonthlySPP(Student $student, $month, $year)
    {
        $monthName = Carbon::create()->month((int)$month)->locale('id')->monthName;

        $fees = FeeMaster::whereHas('category', function ($q) {
                $q->where('billing_interval', 'MONTHLY');
            })
            ->where(function ($q) use ($student) {
                $q->where('unit_target', $student->unit_code)->orWhereNull('unit_target');
            })
            ->where(function ($q) use ($student) {
                $q->where('residence_target', $student->residence_status)->orWhereNull('residence_target');
            })
            ->where(function ($q) use ($month, $year) {
                $targetDate = Carbon::create($year, (int)$month, 1)->toDateString();
                $q->where('start_date', '<=', $targetDate)->orWhereNull('start_date');
            })
            ->where(function ($q) use ($month, $year) {
                $targetDate = Carbon::create($year, (int)$month, 1)->endOfMonth()->toDateString();
                $q->where('end_date', '>=', $targetDate)->orWhereNull('end_date');
            })
            ->get();

        $count = 0;
        foreach ($fees as $fee) {
            $title = $fee->item_name . " " . $monthName . " " . $year;
            $exists = Billing::where('student_id', $student->id)->where('title', $title)->exists();
            if (!$exists) {
                $this->createBillFromFee($student, $fee, $title);
                $count++;
            }
        }
        return $count;
    }

    /**
     * Helper to create a billing record from a FeeMaster item.
     */
    private function createBillFromFee(Student $student, FeeMaster $fee, string $title)
    {
        $amount = $fee->amount;
        $discountAmount = 0;

        if ($student->special_status !== 'UMUM') {
            $discount = Discount::where('fee_master_id', $fee->id)
                ->where('target_status', $student->special_status)
                ->first();

            if ($discount) {
                $discountAmount = $discount->discount_amount;
            }
        }

        $finalAmount = max(0, $amount - $discountAmount);

        return Billing::create([
            'student_id' => $student->id,
            'title' => $title,
            'original_amount' => $amount,
            'discount_applied' => $discountAmount,
            'final_amount' => $finalAmount,
            'status' => 'UNPAID',
        ]);
    }
}
