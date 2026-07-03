<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Student;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ProRataBillingService
 * 
 * Handles pro-rata billing calculations for students joining or leaving mid-month.
 * 
 * Business Rules:
 * 1. Full billing untuk seluruh bulan (default)
 * 2. Pro-rata jika santri baru masuk di tengah bulan (hitung dari join date)
 * 3. Pro-rata jika santri keluar di tengah bulan (hitung sampai left date)
 * 4. Gratis (0%) jika santri tidak hadir >15 hari dalam bulan
 */
class ProRataBillingService
{
    /**
     * Calculate pro-rata billing amount based on student attendance
     * 
     * @param Student $student
     * @param float $monthlyAmount - Full monthly amount
     * @param Carbon $periodStart - Billing period start date
     * @param Carbon $periodEnd - Billing period end date
     * @return array ['type' => 'full|prorate|free', 'amount' => float, 'reason' => string]
     */
    public function calculateProRataAmount(
        Student $student,
        float $monthlyAmount,
        Carbon $periodStart,
        Carbon $periodEnd
    ): array {
        try {
            if ($periodEnd <= $periodStart) {
                throw new Exception('Tanggal berakhir harus setelah tanggal mulai periode.');
            }

            if ($monthlyAmount < 0) {
                throw new Exception('Jumlah biaya bulanan tidak boleh negatif.');
            }

            if (!$student || !$student->exists) {
                throw new Exception('Data santri tidak valid.');
            }

            $effectiveStart = $this->getEffectiveStartDate($student, $periodStart);
            $effectiveEnd = $this->getEffectiveEndDate($student, $periodEnd);

            $totalDays = $periodStart->diffInDays($periodEnd) + 1;
            $activeDays = max(0, $effectiveStart->diffInDays($effectiveEnd) + 1);

            if ($activeDays < 0) {
                throw new Exception('Perhitungan hari aktif tidak valid.');
            }

            if ($this->shouldBeFreeBilling($student, $periodStart, $periodEnd)) {
                return [
                    'type' => 'free',
                    'amount' => 0,
                    'rate' => 0,
                    'reason' => 'Santri absen >15 hari dalam bulan',
                    'calculation' => 'Free (Absensi Tinggi)',
                ];
            }

            if ($activeDays >= $totalDays) {
                return [
                    'type' => 'full',
                    'amount' => $monthlyAmount,
                    'rate' => 100,
                    'reason' => 'Santri aktif sepanjang bulan',
                    'calculation' => "100% × {$monthlyAmount} = {$monthlyAmount}",
                ];
            }

            $rate = ($activeDays / $totalDays) * 100;
            $prorataAmount = ($monthlyAmount / $totalDays) * $activeDays;

            if ($prorataAmount < 0) {
                throw new Exception('Jumlah pro-rata yang dihitung tidak boleh negatif.');
            }

            $prorataAmount = round($prorataAmount / 1000) * 1000;

            return [
                'type' => 'prorate',
                'amount' => $prorataAmount,
                'rate' => round($rate, 2),
                'active_days' => $activeDays,
                'total_days' => $totalDays,
                'reason' => "Santri baru masuk atau keluar di tengah bulan ({$activeDays}/{$totalDays} hari)",
                'calculation' => "{$rate}% × {$monthlyAmount} = {$prorataAmount}",
            ];
        } catch (Exception $e) {
            Log::error('Pro-rata calculation error', [
                'student_id' => $student->id,
                'error' => $e->getMessage(),
            ]);
            throw new Exception("Gagal menghitung pro-rata: {$e->getMessage()}");
        }
    }

    /**
     * Get effective start date (when student is responsible from)
     */
    private function getEffectiveStartDate(Student $student, Carbon $periodStart): Carbon
    {
        if ($student->joined_at && $student->joined_at->gt($periodStart)) {
            return $student->joined_at->startOfDay();
        }

        return $periodStart->startOfDay();
    }

    /**
     * Get effective end date (when student is responsible until)
     */
    private function getEffectiveEndDate(Student $student, Carbon $periodEnd): Carbon
    {
        if ($student->left_at && $student->left_at->lt($periodEnd)) {
            return $student->left_at->startOfDay();
        }

        return $periodEnd->endOfDay();
    }

    /**
     * Check if billing should be free due to high absence
     * Business rule: Free jika santri tidak hadir >15 hari
     */
    private function shouldBeFreeBilling(Student $student, Carbon $periodStart, Carbon $periodEnd): bool
    {
        $attendanceData = DB::table('attendances')
            ->where('student_id', $student->id)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->where('status', 'absent')
            ->count();

        $totalDays = $periodStart->diffInDays($periodEnd) + 1;
        $absentDays = $attendanceData;

        return $absentDays > 15;
    }

    /**
     * Create billing with pro-rata calculation
     */
    public function createProRataBilling(
        Student $student,
        int $feeMasterId,
        string $title,
        float $monthlyAmount,
        ?float $discount = null,
        ?Carbon $periodStart = null,
        ?Carbon $periodEnd = null
    ): Billing {
        $periodStart = $periodStart ?? now()->startOfMonth();
        $periodEnd = $periodEnd ?? now()->endOfMonth();

        if ($student->status !== 'ACTIVE' && $student->status !== \App\Enums\StudentStatus::ACCEPTED->value) {
            throw new Exception("Hanya santri ACTIVE yang dapat ditagih");
        }

        $calculation = $this->calculateProRataAmount(
            $student,
            $monthlyAmount,
            $periodStart,
            $periodEnd
        );

        $originalAmount = $calculation['amount'];
        $discountAmount = min($discount ?? 0, $originalAmount); 
        $finalAmount = max(0, $originalAmount - $discountAmount);

        return DB::transaction(function () use (
            $student,
            $feeMasterId,
            $title,
            $originalAmount,
            $discountAmount,
            $finalAmount,
            $calculation,
            $periodStart,
            $periodEnd
        ) {
            $billing = Billing::create([
                'student_id' => $student->id,
                'fee_master_id' => $feeMasterId,
                'title' => $title,
                'original_amount' => $originalAmount,
                'discount_applied' => $discountAmount,
                'final_amount' => $finalAmount,
                'status' => 'UNPAID',
                'billing_period_start' => $periodStart,
                'billing_period_end' => $periodEnd,
                'billing_generated_at' => now(),
                'expires_at' => $periodEnd->addDays(30),
                'proration_type' => $calculation['type'],
                'proration_rate' => $calculation['rate'],
                'proration_note' => $calculation['reason'],
                'price_snapshot' => json_encode([
                    'title' => $title,
                    'monthly_amount' => $monthlyAmount,
                    'calculation' => $calculation['calculation'],
                    'proration' => $calculation,
                ]),
            ]);

            Log::info('Pro-rata billing created', [
                'billing_id' => $billing->id,
                'student_id' => $student->id,
                'type' => $calculation['type'],
                'rate' => $calculation['rate'],
                'amount' => $finalAmount,
            ]);

            return $billing;
        });
    }

    /**
     * Generate report of all pro-rata billings
     */
    public function getProrataReport(Carbon $from, Carbon $to): array
    {
        $billings = Billing::whereBetween('billing_generated_at', [$from, $to])
            ->whereIn('proration_type', ['prorate', 'free'])
            ->get();

        $report = [
            'period' => "{$from->format('Y-m-d')} to {$to->format('Y-m-d')}",
            'total_billings' => $billings->count(),
            'full_billings' => $billings->where('proration_type', 'full')->count(),
            'prorate_billings' => $billings->where('proration_type', 'prorate')->count(),
            'free_billings' => $billings->where('proration_type', 'free')->count(),
            'total_amount' => $billings->sum('final_amount'),
            'discount_amount' => $billings->sum('discount_applied'),
            'prorate_details' => $billings
                ->where('proration_type', 'prorate')
                ->map(fn($b) => [
                    'student_name' => $b->student->name,
                    'rate' => $b->proration_rate,
                    'reason' => $b->proration_note,
                    'amount' => $b->final_amount,
                ])
                ->values(),
        ];

        return $report;
    }
}
