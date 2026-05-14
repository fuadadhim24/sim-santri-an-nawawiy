<?php

namespace Database\Seeders;

use App\Models\Billing;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
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
        $admin = User::where('role', 'SUPER_ADMIN')->first();

        if (!$student || !$admin) {
            $this->command->info('No students or admin found. Please run UserSeeder first.');
            return;
        }

        $this->command->info('Seeding billings for student: ' . $student->full_name);

        // Billing 1: Paid
        $billing1 = Billing::create([
            'student_id' => $student->id,
            'title' => 'SPP Januari ' . date('Y'),
            'original_amount' => 500,
            'discount_applied' => 0,
            'final_amount' => 500,
            'status' => 'PAID',
            'created_at' => Carbon::now()->subMonth()->startOfMonth(),
            'updated_at' => Carbon::now()->subMonth()->endOfMonth(),
        ]);

        // Create payment record for paid billing
        Payment::create([
            'billing_id' => $billing1->id,
            'amount' => 500,
            'method' => 'CASH',
            'admin_id' => $admin->id,
            'paid_at' => Carbon::now()->subMonth()->endOfMonth(),
            'status' => 'paid',
        ]);

        // Billing 2: Unpaid
        Billing::create([
            'student_id' => $student->id,
            'title' => 'SPP ' . Carbon::now()->isoFormat('MMMM Y'),
            'original_amount' => 800,
            'discount_applied' => 0,
            'final_amount' => 800,
            'status' => 'UNPAID',
            'created_at' => Carbon::now()->startOfMonth(),
            'updated_at' => Carbon::now()->startOfMonth(),
        ]);

        // Billing 3: Unpaid
        Billing::create([
            'student_id' => $student->id,
            'title' => 'Uang Pangkal / Gedung',
            'original_amount' => 1300,
            'discount_applied' => 0,
            'final_amount' => 1300,
            'status' => 'UNPAID',
            'created_at' => Carbon::now()->subMonths(2),
            'updated_at' => Carbon::now()->subMonths(2),
        ]);
    }
}

