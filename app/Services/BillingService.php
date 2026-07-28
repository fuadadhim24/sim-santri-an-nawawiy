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

class BillingService
{
    /**
     * Generate a bill for a student based on a specific fee category and item name.
     * This handles finding the correct fee for the student's unit/residence and applying discounts.
     */
    public function generateBill(Student $student, int $feeCategoryId, string $title, ?string $feeItemName = null)
    {
        $category = FeeCategory::find($feeCategoryId);
        if (!$category) {
            return null;
        }

        $this->validateBillingCreation($category, $student);

        $query = FeeMaster::where('fee_category_id', $feeCategoryId)
            ->where(function ($q) use ($student) {
                $q->where('unit_target', $student->unit_code)
                  ->orWhereNull('unit_target');
            })
            ->where(function ($q) use ($student) {
                $q->where('residence_target', $student->residence_status)
                  ->orWhereNull('residence_target');
            })
            ->where(function ($q) use ($student) {
                $q->where('class_level_target_id', $student->class_level_id)
                  ->orWhereNull('class_level_target_id');
            });

        if ($feeItemName) {
            $query->where('item_name', $feeItemName);
        }

        $fees = $query->get();

        if ($fees->isEmpty()) {
            return null;
        }

        $firstFee = $fees->first();

        if ($category->isSingleActivePerKey()) {
            $key = $this->getBillingKey($firstFee, $student);
            $this->deactivateOldBillings($key);
        }

        $totalOriginalAmount = 0;
        $totalDiscount = 0;
        $discounts = collect();

        if ($student->hasAnySpecialStatus()) {
            $statusCodes = $student->getSpecialStatusCodes();
            $feeIds = $fees->pluck('id');
            $discounts = Discount::whereIn('fee_master_id', $feeIds)
                ->whereIn('target_status', $statusCodes)
                ->get()
                ->groupBy('fee_master_id');
        }

        foreach ($fees as $fee) {
            $amount = $fee->amount;
            $discountAmount = 0;

            if ($student->hasAnySpecialStatus()) {
                $feeDiscounts = $discounts[$fee->id] ?? collect();
                foreach ($feeDiscounts as $d) {
                    $discountAmount += $d->discount_amount;
                }
            }

            $totalOriginalAmount += $amount;
            $totalDiscount += $discountAmount;
        }

        if ($totalDiscount > $totalOriginalAmount) {
            $totalDiscount = $totalOriginalAmount;
        }

        $finalAmount = $totalOriginalAmount - $totalDiscount;
        $dueDate = $firstFee ? now()->addDays($firstFee->due_days ?? 14)->format('Y-m-d') : null;

        return Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $firstFee?->id,
            'title' => $title,
            'original_amount' => $totalOriginalAmount,
            'discount_applied' => $totalDiscount,
            'final_amount' => $finalAmount,
            'status' => 'UNPAID',
            'due_date' => $dueDate,
        ]);
    }

    /**
     * Get the billing key for a fee master and student.
     * This key defines uniqueness for billing records.
     */
    private function getBillingKey(FeeMaster $feeMaster, Student $student): array
    {
        $category = $feeMaster->category;

        if (!$category) {
            return [];
        }

        $key = [
            'student_id' => $student->id,
            'fee_category_id' => $category->id,
        ];

        if ($feeMaster->billing_month) {
            $key['billing_month'] = $feeMaster->billing_month;
        }

        if ($feeMaster->unit_target) {
            $key['unit_target'] = $feeMaster->unit_target;
        }

        if ($feeMaster->residence_target) {
            $key['residence_target'] = $feeMaster->residence_target;
        }

        return $key;
    }

    /**
     * Validate billing creation based on category rules.
     */
    private function validateBillingCreation(FeeCategory $category, Student $student): void
    {
        if ($category->requiresAcceptance()) {
            if (!$student->isAccepted()) {
                throw new Exception("Tagihan kategori {$category->name} hanya dapat dibuat untuk siswa dengan status 'diterima'. Status siswa saat ini: {$student->status}");
            }
        }

        if ($category->isManualOnly()) {
            throw new Exception("Tagihan kategori {$category->name} hanya dapat dibuat secara manual melalui antarmuka admin.");
        }

        if ($category->unit_target && $category->unit_target !== $student->unit_code) {
            throw new Exception("Kategori {$category->name} diperuntukkan bagi unit {$category->unit_target}, sementara siswa berada di unit {$student->unit_code}.");
        }

        if ($category->domicile_target && $category->domicile_target !== $student->residence_status) {
            throw new Exception("Kategori {$category->name} diperuntukkan bagi domisili {$category->domicile_target}, sementara siswa berstatus {$student->residence_status}.");
        }
    }

    /**
     * Deactivate old billings that match the given key.
     */
    private function deactivateOldBillings(array $key): void
    {
        if (empty($key)) {
            return;
        }

        $query = Billing::where('status', 'UNPAID');

        foreach ($key as $field => $value) {
            $query->where($field, $value);
        }

        $billings = $query->get();

        foreach ($billings as $billing) {
            $billing->update(['visible_to_wali' => false]);

            Log::info('Deactivated old billing', [
                'billing_id' => $billing->id,
                'title' => $billing->title,
                'student_id' => $billing->student_id,
                'key' => $key,
            ]);
        }
    }

    /**
     * Eager load discounts for a collection of fees based on student's special status.
     */
    private function loadDiscountsForFees($fees, Student $student)
    {
        if (!$student->hasAnySpecialStatus() || $fees->isEmpty()) {
            return collect();
        }

        $statusCodes = $student->getSpecialStatusCodes();
        $feeIds = $fees->pluck('id');
        return Discount::whereIn('fee_master_id', $feeIds)
            ->whereIn('target_status', $statusCodes)
            ->get()
            ->groupBy('fee_master_id');
    }

    /**
     * Helper to create a billing record from a FeeMaster item.
     */
    private function createBillFromFee(Student $student, FeeMaster $fee, string $title, $discounts = null)
    {
        $category = $fee->category;

        if ($category) {
            $this->validateBillingCreation($category, $student);

            if ($category->isSingleActivePerKey()) {
                $key = $this->getBillingKey($fee, $student);
                $this->deactivateOldBillings($key);
            }
        }

        $amount = $fee->amount;
        $discountAmount = 0;

        if ($student->hasAnySpecialStatus()) {
            if ($discounts !== null) {
                // discounts is now grouped by fee_master_id
                $feeDiscounts = $discounts[$fee->id] ?? collect();
                foreach ($feeDiscounts as $d) {
                    $discountAmount += $d->discount_amount;
                }
            } else {
                $statusCodes = $student->getSpecialStatusCodes();
                $discountAmount = Discount::where('fee_master_id', $fee->id)
                    ->whereIn('target_status', $statusCodes)
                    ->sum('discount_amount');
            }
        }

        if ($discountAmount > $amount) {
            $discountAmount = $amount;
        }

        $finalAmount = max(0, $amount - $discountAmount);
        $dueDate = now()->addDays($fee->due_days ?? 14)->format('Y-m-d');

        return Billing::create([
            'student_id' => $student->id,
            'fee_master_id' => $fee->id,
            'title' => $title,
            'original_amount' => $amount,
            'discount_applied' => $discountAmount,
            'final_amount' => $finalAmount,
            'status' => 'UNPAID',
            'due_date' => $dueDate,
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

        if ($student->hasAnySpecialStatus()) {
            $statusCodes = $student->getSpecialStatusCodes();
            $totalDiscount = Discount::where('fee_master_id', $feeMaster->id)
                ->whereIn('target_status', $statusCodes)
                ->sum('discount_amount');
        }

        if ($totalDiscount > $totalAmount) {
            $totalDiscount = $totalAmount;
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
            ->where(function ($q) use ($student) {
                $q->where('class_level_target_id', $student->class_level_id)->orWhereNull('class_level_target_id');
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
        $skipped = 0;

        DB::transaction(function () use ($student, $feeMasterIds, &$count, &$skipped) {
            $fees = FeeMaster::whereIn('id', $feeMasterIds)
                ->with('category')
                ->get();

            $discounts = $this->loadDiscountsForFees($fees, $student);

            foreach ($fees as $fee) {
                $category = $fee->category;

                if ($category && $category->isManualOnly()) {
                    $skipped++;
                    continue;
                }

                $this->createBillFromFee($student, $fee, $fee->item_name, $discounts);
                $count++;
            }
        });

        Log::info('Generated once bills for new student', [
            'student_id' => $student->id,
            'fee_master_ids' => $feeMasterIds,
            'count' => $count,
            'skipped' => $skipped,
        ]);

        return $count;
    }

    /**
     * Generate all required billings for a student when they are accepted.
     * This method creates billings for all fee categories that can be generated after acceptance.
     */
    public function generateBillingsForAcceptedStudent(Student $student): int
    {
        $count = 0;

        DB::transaction(function () use ($student, &$count) {
            // Get all fee categories that can be generated after acceptance
            $feeCategories = FeeCategory::where('can_generate_before_acceptance', false)
                ->where('is_active', true)
                ->where('is_locked', false)
                ->whereNotIn('activation_mode', ['MANUAL_ONLY'])
                ->where(function ($q) use ($student) {
                    $q->where('unit_target', $student->unit_code)
                      ->orWhereNull('unit_target');
                })
                ->where(function ($q) use ($student) {
                    $q->where('domicile_target', $student->residence_status)
                      ->orWhereNull('domicile_target');
                })
                ->get();

            foreach ($feeCategories as $category) {
                // Find all fee masters for this category that match the student's profile
                $feeMasters = FeeMaster::where('fee_category_id', $category->id)
                    ->activeWithinDates()
                    ->where(function ($q) use ($student) {
                        $q->where('unit_target', $student->unit_code)
                          ->orWhereNull('unit_target');
                    })
                    ->where(function ($q) use ($student) {
                        $q->where('residence_target', $student->residence_status)
                          ->orWhereNull('residence_target');
                    })
                    ->where(function ($q) use ($student) {
                        $q->where('class_level_target_id', $student->class_level_id)
                          ->orWhereNull('class_level_target_id');
                    })
                    ->get();

                foreach ($feeMasters as $feeMaster) {
                    try {
                        $this->createBillFromFee($student, $feeMaster, $feeMaster->item_name);
                        $count++;
                    } catch (Exception $e) {
                        Log::warning('Failed to generate billing for accepted student', [
                            'student_id' => $student->id,
                            'fee_master_id' => $feeMaster->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        });

        Log::info('Generated billings for accepted student', [
            'student_id' => $student->id,
            'student_name' => $student->full_name,
            'billings_generated' => $count,
        ]);

        return $count;
    }

    /**
     * Generate billings for a student with specific fee categories selected
     */
    public function generateBillingsForStudentWithCategories(Student $student, array $feeCategoryIds): int
    {
        $count = 0;

        DB::transaction(function () use ($student, $feeCategoryIds, &$count) {
            // Get only the selected fee categories
            $feeCategories = FeeCategory::whereIn('id', $feeCategoryIds)
                ->where('is_active', true)
                ->where('is_locked', false)
                ->where(function ($q) use ($student) {
                    $q->where('unit_target', $student->unit_code)
                      ->orWhereNull('unit_target');
                })
                ->where(function ($q) use ($student) {
                    $q->where('domicile_target', $student->residence_status)
                      ->orWhereNull('domicile_target');
                })
                ->get();

            foreach ($feeCategories as $category) {
                // Find all fee masters for this category that match the student's profile
                $feeMasters = FeeMaster::where('fee_category_id', $category->id)
                    ->activeWithinDates()
                    ->where(function ($q) use ($student) {
                        $q->where('unit_target', $student->unit_code)
                          ->orWhereNull('unit_target');
                    })
                    ->where(function ($q) use ($student) {
                        $q->where('residence_target', $student->residence_status)
                          ->orWhereNull('residence_target');
                    })
                    ->where(function ($q) use ($student) {
                        $q->where('class_level_target_id', $student->class_level_id)
                          ->orWhereNull('class_level_target_id');
                    })
                    ->get();

                foreach ($feeMasters as $feeMaster) {
                    try {
                        $this->createBillFromFee($student, $feeMaster, $feeMaster->item_name);
                        $count++;
                    } catch (Exception $e) {
                        Log::warning('Failed to generate billing for selected category', [
                            'student_id' => $student->id,
                            'fee_category_id' => $category->id,
                            'fee_master_id' => $feeMaster->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        });

        Log::info('Generated billings for selected categories', [
            'student_id' => $student->id,
            'student_name' => $student->full_name,
            'categories_count' => count($feeCategoryIds),
            'billings_generated' => $count,
        ]);

        return $count;
    }

    /**
     * Get unpaid billings for a student.
     */
    public function getUnpaidBillings(Student $student)
    {
        return Billing::where('student_id', $student->id)
            ->where('status', 'UNPAID')
            ->where('visible_to_wali', true)
            ->with('feeMaster.category')
            ->get();
    }

    /**
     * Handle transition of billings when a student's profile changes (unit, residence, class level).
     */
    public function transitionStudentBillings(
        Student $student,
        string $oldUnpaidPolicy,
        array $oldBillingsToDelete,
        array $newCategoryIdsToGenerate
    ): array {
        $deletedCount = 0;
        $generatedCount = 0;

        DB::transaction(function () use (
            $student,
            $oldUnpaidPolicy,
            $oldBillingsToDelete,
            $newCategoryIdsToGenerate,
            &$deletedCount,
            &$generatedCount
        ) {
            // 1. Process old unpaid billings based on the policy
            if ($oldUnpaidPolicy === 'delete_all') {
                $unpaidBillings = Billing::where('student_id', $student->id)
                    ->where('status', 'UNPAID')
                    ->get();

                foreach ($unpaidBillings as $billing) {
                    $billing->delete(); // Soft delete
                    $deletedCount++;
                }
            } elseif ($oldUnpaidPolicy === 'delete_except_current_month') {
                $unpaidBillings = Billing::where('student_id', $student->id)
                    ->where('status', 'UNPAID')
                    ->get();

                $currentMonth = now()->format('Y-m');

                foreach ($unpaidBillings as $billing) {
                    $createdAtMonth = $billing->created_at ? $billing->created_at->format('Y-m') : '';
                    $dueDateMonth = $billing->due_date ? $billing->due_date->format('Y-m') : '';

                    if ($createdAtMonth !== $currentMonth && $dueDateMonth !== $currentMonth) {
                        $billing->delete();
                        $deletedCount++;
                    }
                }
            } elseif ($oldUnpaidPolicy === 'delete_selected') {
                if (!empty($oldBillingsToDelete)) {
                    $billings = Billing::where('student_id', $student->id)
                        ->where('status', 'UNPAID')
                        ->whereIn('id', $oldBillingsToDelete)
                        ->get();

                    foreach ($billings as $billing) {
                        $billing->delete();
                        $deletedCount++;
                    }
                }
            }
            // If policy is 'keep_all', we do nothing

            // 2. Generate new billings matching the new profile
            if (!empty($newCategoryIdsToGenerate)) {
                $generatedCount = $this->generateBillingsForStudentWithCategories($student, $newCategoryIdsToGenerate);
            }

            Log::info('Student billing transition completed', [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'policy' => $oldUnpaidPolicy,
                'deleted_count' => $deletedCount,
                'generated_count' => $generatedCount,
                'operator_id' => auth()->id() ?? 1,
            ]);
        });

        return [
            'deleted' => $deletedCount,
            'generated' => $generatedCount
        ];
    }
}
