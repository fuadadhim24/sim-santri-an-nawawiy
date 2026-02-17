<?php

namespace Database\Seeders;

use App\Models\Billing;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BillingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = Student::first();

        if (!$student) {
            $this->command->info('No students found. Please run UserSeeder first.');
            return;
        }

        $this->command->info('Seeding billings for student: ' . $student->full_name);

        Billing::create([
            'student_id' => $student->id,
            'title' => 'SPP Januari ' . date('Y'),
            'original_amount' => 500000,
            'discount_applied' => 0,
            'final_amount' => 500000,
            'status' => 'PAID',
            'created_at' => Carbon::now()->subMonth()->startOfMonth(),
            'updated_at' => Carbon::now()->subMonth()->endOfMonth(),
        ]);

        Billing::create([
            'student_id' => $student->id,
            'title' => 'SPP ' . Carbon::now()->isoFormat('MMMM Y'),
            'original_amount' => 500000,
            'discount_applied' => 0,
            'final_amount' => 500000,
            'status' => 'UNPAID',
            'created_at' => Carbon::now()->startOfMonth(),
            'updated_at' => Carbon::now()->startOfMonth(),
        ]);

        Billing::create([
            'student_id' => $student->id,
            'title' => 'Uang Pangkal / Gedung',
            'original_amount' => 2500000,
            'discount_applied' => 0,
            'final_amount' => 2500000,
            'status' => 'UNPAID',
            'created_at' => Carbon::now()->subMonths(2),
            'updated_at' => Carbon::now()->subMonths(2),
        ]);
    }
}
