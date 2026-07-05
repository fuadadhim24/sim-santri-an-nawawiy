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
        $nextYear = $currentYear + 1;
        $yearAfterNext = $currentYear + 2;

        $schedules = [
            [
                'name' => "Gelombang 1 - Pendaftaran Santri Baru {$currentYear}/{$nextYear}",
                'description' => 'Gelombang pertama pendaftaran santri baru untuk tahun ajaran ' . $currentYear . '/' . $nextYear,
                'registration_start' => now()->subDays(5),
                'registration_end' => now()->addDays(15),
                'is_active' => true,
            ],
            [
                'name' => "Gelombang 2 - Pendaftaran Santri Baru {$currentYear}/{$nextYear}",
                'description' => 'Gelombang kedua pendaftaran santri baru untuk tahun ajaran ' . $currentYear . '/' . $nextYear,
                'registration_start' => now()->addDays(16),
                'registration_end' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'name' => "Pendaftaran Santri Baru Tahun Ajaran {$nextYear}/{$yearAfterNext}",
                'description' => 'Pendaftaran santri baru untuk tahun ajaran ' . $nextYear . '/' . $yearAfterNext,
                'registration_start' => now()->addMonths(6),
                'registration_end' => now()->addMonths(9),
                'is_active' => false,
            ],
        ];

        foreach ($schedules as $schedule) {
            SpmbSchedule::firstOrCreate(
                ['name' => $schedule['name']],
                $schedule
            );
        }
    }
}
