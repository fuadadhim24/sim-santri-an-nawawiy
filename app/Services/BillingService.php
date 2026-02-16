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
    public function generateBill(Student $student, string $category, string $title, ?string $feeItemName = null)
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

        $query = FeeMaster::where('category', $category)
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
     * Generate monthly SPP bill for a student.
     */
    public function generateMonthlySPP(Student $student, $month, $year)
    {
        $monthName = Carbon::create()->month($month)->locale('id')->monthName;
        $title = "SPP " . $monthName . " " . $year;

        // Ensure we don't generate duplicate SPP for same month?
        $exists = Billing::where('student_id', $student->id)
            ->where('title', $title)
            ->exists();

        if ($exists) {
            return null;
        }

        return $this->generateBill($student, 'BULANAN', $title);
    }
}
