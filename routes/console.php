<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('app:generate-due-billings', function (\App\Services\BillingService $billingService) {
    $this->info("Memulai pengecekan tagihan jatuh tempo...");

    $students = \App\Models\Student::where('is_active', true)
        ->where('residence_status', '!=', 'NGAJI_ONLY')
        ->get();

    $totalGenerated = 0;
    $dayToday = date('j');
    $month = date('n');
    $year = date('Y');

    foreach ($students as $student) {
        // Only generate if there is a fee due today
        $hasDueFee = \App\Models\FeeMaster::where('billing_interval', 'MONTHLY')
            ->where('billing_day', $dayToday)
            ->where(function ($q) use ($student) {
                $q->where('unit_target', $student->unit_code)->orWhereNull('unit_target');
            })
            ->exists();

        if ($hasDueFee) {
            $totalGenerated += $billingService->generateMonthlySPP($student, $month, $year);
        }

        // Also check for ONCE and YEARLY? Usually manual or at start-of-year.
        // Let's stick to MONTHLY automation for now as it's the most recurring one.
    }

    $this->info("Selesai. Berhasil menerbitkan $totalGenerated tagihan baru hari ini.");
})->purpose('Otomatis menerbitkan tagihan bulanan berdasarkan tanggal jatuh tempo yang diatur di Master Biaya.')->hourly();
