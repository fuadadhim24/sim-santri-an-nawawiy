<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('cache:clear')
    ->dailyAt('01:00')
    ->withoutOverlapping()
    ->onOneServer();

// Menjalankan pembuatan tagihan rutinan (Bulanan/Tahunan) setiap jam 00:00 (tengah malam)
Schedule::command('billing:generate-recurring')
    ->daily()
    ->withoutOverlapping()
    ->onOneServer();
