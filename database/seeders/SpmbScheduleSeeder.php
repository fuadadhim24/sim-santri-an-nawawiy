<?php

namespace Database\Seeders;

use App\Models\SpmbSchedule;
use Illuminate\Database\Seeder;

class SpmbScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = now()->year;

        // Create active SPMB schedule for current year
        $nextYear = $currentYear + 1;
        SpmbSchedule::create([
            'name' => "Pendaftaran Santri Baru Tahun Ajaran {$currentYear}/{$nextYear}",
            'description' => 'Pendaftaran santri baru untuk tahun ajaran ' . $currentYear . '/' . $nextYear,
            'registration_start' => now()->subDays(5),
            'registration_end' => now()->addDays(30),
            'is_active' => true,
        ]);

        // Create another schedule for next year (not active yet)
        $yearAfterNext = $currentYear + 2;
        SpmbSchedule::create([
            'name' => "Pendaftaran Santri Baru Tahun Ajaran {$nextYear}/{$yearAfterNext}",
            'description' => 'Pendaftaran santri baru untuk tahun ajaran ' . $nextYear . '/' . $yearAfterNext,
            'registration_start' => now()->addMonths(6),
            'registration_end' => now()->addMonths(9),
            'is_active' => false,
        ]);
    }
}
