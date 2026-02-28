<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('app:generate-due-billings', function (\App\Services\BillingService $billingService) {
    $this->info("Memulai pengecekan tagihan jatuh tempo...");

    $students = \App\Models\Student::where('is_active', true)
        ->where('residence_status', '!=', 'NGAJI_ONLY')
        ->get();

    $totalGenerated = 0;
    $dayToday = now()->day;
    $month = now()->month;
    $year = now()->year;
    $isFirstDayOfYear = $dayToday === 1 && $month === 1;

    foreach ($students as $student) {
        // Check if student has any monthly fees due today
        // Filters by: billing_day, unit_target, residence_target
        // Note: BillingService::generateMonthlySPP applies the same filters internally
        $hasMonthlyDue = \App\Models\FeeMaster::whereHas('category', function ($q) {
                $q->where('billing_interval', 'MONTHLY');
            })
            ->where('billing_day', $dayToday)
            ->where(function ($q) use ($student) {
                $q->where('unit_target', $student->unit_code)->orWhereNull('unit_target');
            })
            ->where(function ($q) use ($student) {
                $q->where('residence_target', $student->residence_status)->orWhereNull('residence_target');
            })
            ->exists();

        if ($hasMonthlyDue) {
            $totalGenerated += $billingService->generateMonthlySPP($student, $month, $year);
        }

        if ($isFirstDayOfYear) {
            $hasYearlyDue = \App\Models\FeeMaster::whereHas('category', function ($q) {
                    $q->where('billing_interval', 'YEARLY');
                })
                ->where(function ($q) use ($student) {
                    $q->where('unit_target', $student->unit_code)->orWhereNull('unit_target');
                })
                ->where(function ($q) use ($student) {
                    $q->where('residence_target', $student->residence_status)->orWhereNull('residence_target');
                })
                ->exists();

            if ($hasYearlyDue) {
                $totalGenerated += $billingService->generateYearlyBills($student, $year);
            }
        }
    }

    $this->info("Selesai. Berhasil menerbitkan $totalGenerated tagihan baru hari ini.");
})->purpose('Otomatis menerbitkan tagihan bulanan/tahunan berdasarkan tanggal jatuh tempo.');

Schedule::command('app:generate-due-billings')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->onOneServer();
