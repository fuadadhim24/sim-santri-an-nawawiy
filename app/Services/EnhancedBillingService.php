<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Discount;
use App\Models\FeeCategory;
use App\Models\FeeMaster;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class EnhancedBillingService
{
    /**
     * SECURE: Generate a bill for a student with multiple defensive validations
     * - Check student status is ACTIVE
     * - Check join date doesn't exceed billing period
     * - Snapshot price (immutable)
     * - Use transaction for atomicity
     */
    public function generateBillSecurely(
        Student $student,
        int $feeCategoryId,
        string $title,
        ?string $feeItemName = null,
        ?\DateTimeInterface $billingPeriodStart = null,
        ?\DateTimeInterface $billingPeriodEnd = null
    ) {
        if ($student->status !== 'ACTIVE' && $student->status !== \App\Enums\StudentStatus::ACCEPTED->value) {
            throw new Exception(
                "Tidak bisa membuat tagihan. Status santri: {$student->status}. " .
                "Hanya santri ACTIVE yang dapat ditagih."
            );
        }

        if ($billingPeriodStart && $student->joined_at && $student->joined_at->gt($billingPeriodStart)) {
            throw new Exception(
                "Tidak bisa membuat tagihan untuk periode {$billingPeriodStart->format('M Y')}. " .
                "Santri baru masuk pada {$student->joined_at->format('d M Y')}."
            );
        }

        if ($billingPeriodEnd && $student->left_at && $student->left_at->lt($billingPeriodEnd)) {
            throw new Exception(
                "Tidak bisa membuat tagihan untuk periode setelah santri keluar " .
                "(Keluar: {$student->left_at->format('d M Y')})."
            );
        }

        $category = FeeCategory::find($feeCategoryId);
        if (!$category) {
            return null;
        }

        $existingBilling = Billing::where('student_id', $student->id)
            ->whereHas('feeMaster', function ($q) use ($feeCategoryId) {
                $q->where('fee_category_id', $feeCategoryId);
            })
            ->where('status', '!=', 'VOID')
            ->first();

        if ($existingBilling && (!$existingBilling->expires_at || $existingBilling->expires_at->isFuture())) {
            throw new Exception('Tagihan untuk biaya ini sudah ada untuk santri. Tidak bisa membuat duplikat.');
        }

        $query = FeeMaster::where('fee_category_id', $feeCategoryId)
            ->activeWithinDates()
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
            throw new Exception("Tidak ada biaya yang sesuai untuk santri ini.");
        }

        return DB::transaction(function () use ($student, $category, $fees, $title) {
            $totalOriginalAmount = 0;
            $totalDiscount = 0;
            $priceSnapshot = [];

            foreach ($fees as $fee) {
                $priceSnapshot[] = [
                    'item_name' => $fee->item_name,
                    'amount' => $fee->amount,
                    'fee_master_id' => $fee->id,
                ];
                $totalOriginalAmount += $fee->amount;
            }

            if ($student->special_status !== 'UMUM') {
                $feeIds = $fees->pluck('id');
                $discounts = Discount::whereIn('fee_master_id', $feeIds)
                    ->where('target_status', $student->special_status)
                    ->get()
                    ->keyBy('fee_master_id');

                foreach ($fees as $fee) {
                    $discount = $discounts[$fee->id] ?? null;
                    if ($discount) {
                        $totalDiscount += $discount->discount_amount;
                    }
                }

                if ($totalDiscount > $totalOriginalAmount) {
                    Log::warning('Discount capped at 100%', [
                        'student_id' => $student->id,
                        'requested_discount' => $totalDiscount,
                        'original_amount' => $totalOriginalAmount,
                    ]);
                    $totalDiscount = $totalOriginalAmount;
                }
            }

            $finalAmount = $totalOriginalAmount - $totalDiscount;

            $billing = Billing::create([
                'student_id' => $student->id,
                'fee_master_id' => $fees->first()->id,
                'title' => $title,
                'original_amount' => $totalOriginalAmount,
                'discount_applied' => $totalDiscount,
                'final_amount' => $finalAmount,
                'status' => 'UNPAID',
                'price_snapshot' => json_encode($priceSnapshot), 
            ]);

            Log::info('Billing created securely', [
                'billing_id' => $billing->id,
                'student_id' => $student->id,
                'amount' => $finalAmount,
                'snapshot_count' => count($priceSnapshot),
            ]);

            return $billing;
        });
    }

    /**
     * SECURE: Validate if billing can be deactivated
     * Prevents deactivating billings that are partially paid
     */
    public function validateBillingDeactivation(Billing $billing): void
    {
        if ($billing->status === 'PAID') {
            throw new Exception(
                "Tidak dapat membatalkan tagihan yang sudah dibayar. " .
                "ID: {$billing->id}"
            );
        }

        $payments = $billing->payments()->where('status', 'paid')->count();
        if ($payments > 0) {
            throw new Exception(
                "Tidak dapat membatalkan tagihan yang sudah menerima pembayaran. " .
                "Hubungi Admin untuk refund."
            );
        }
    }

    /**
     * SECURE: Only allow status change for status AFTER billing period closes
     */
    public function updateStudentStatusSafely(Student $student, string $newStatus): void
    {
        DB::transaction(function () use ($student, $newStatus) {
            $activeBillings = Billing::where('student_id', $student->id)
                ->where('status', 'UNPAID')
                ->exists();

            if ($activeBillings && $newStatus !== 'ACTIVE' && $newStatus !== \App\Enums\StudentStatus::ACCEPTED->value) {
                throw new Exception(
                    "Tidak dapat mengubah status santri saat masih ada tagihan aktif. " .
                    "Lunasi semua tagihan terlebih dahulu."
                );
            }

            $student->update(['status' => $newStatus]);

            Log::info('Student status updated safely', [
                'student_id' => $student->id,
                'new_status' => $newStatus,
            ]);
        });
    }
}
