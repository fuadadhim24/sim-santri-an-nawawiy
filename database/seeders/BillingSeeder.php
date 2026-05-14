<?php

namespace Database\Seeders;

use App\Models\Billing;
use App\Models\FeeMaster;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BillingSeeder extends Seeder
{
    /**
     * Seed billings & payments yang merepresentasikan semua status:
     *
     * Billing Status: PAID, UNPAID, EXPIRED, VOID
     * Payment Status: paid, pending, failed
     * Payment Method: cash, duitku
     */
    public function run(): void
    {
        // Disable observers during seeding (no authenticated user for audit logs)
        Billing::withoutEvents(function () {
        Payment::withoutEvents(function () {
        $admin = User::where('role', 'SUPER_ADMIN')->first();

        // Santri diterima (untuk billing)
        $student1 = Student::where('full_name', 'Ahmad Santri')->first();   // SMP, UMUM
        $student4 = Student::where('full_name', 'Muhammad Ridwan Jr')->first(); // SMA, ANAK_GURU
        $student5 = Student::where('full_name', 'Aisyah binti Ridwan')->first(); // PPTQ, UMUM
        $student6 = Student::where('full_name', 'Zahra binti Rahmah')->first(); // SMP, YATIM

        // Fee Masters
        $fmSPP_SMP  = FeeMaster::where('item_name', 'SPP SMP')->first();
        $fmSPP_SMA  = FeeMaster::where('item_name', 'SPP SMA')->first();
        $fmSPP_PPTQ = FeeMaster::where('item_name', 'SPP PPTQ')->first();
        $fmSPMB     = FeeMaster::where('item_name', 'Biaya Pendaftaran SPMB')->first();
        $fmAsrama   = FeeMaster::where('item_name', 'Biaya Asrama Bulanan')->first();
        $fmDaftarUlang = FeeMaster::where('item_name', 'Biaya Daftar Ulang Semester')->first();

        if (!$student1 || !$fmSPP_SMP) {
            $this->command->info('Skipping BillingSeeder: required data not found.');
            return;
        }

        // ══════════════════════════════════════════════
        // STUDENT 1 (Ahmad) — SPP SMP Januari (PAID, cash)
        // ══════════════════════════════════════════════
        $billing1 = Billing::create([
            'student_id' => $student1->id,
            'fee_master_id' => $fmSPP_SMP?->id,
            'title' => 'SPP SMP Januari 2026',
            'original_amount' => $fmSPP_SMP->amount,
            'discount_applied' => 0,
            'final_amount' => $fmSPP_SMP->amount,
            'status' => 'PAID',
            'created_at' => Carbon::parse('2026-01-05'),
        ]);

        Payment::create([
            'billing_id' => $billing1->id,
            'admin_id' => $admin->id,
            'amount' => $fmSPP_SMP->amount,
            'method' => 'cash',
            'status' => 'paid',
            'paid_at' => Carbon::parse('2026-01-10'),
        ]);

        // ══════════════════════════════════════════════
        // STUDENT 1 (Ahmad) — SPP SMP Februari (PAID, duitku)
        // ══════════════════════════════════════════════
        $billing2 = Billing::create([
            'student_id' => $student1->id,
            'fee_master_id' => $fmSPP_SMP?->id,
            'title' => 'SPP SMP Februari 2026',
            'original_amount' => $fmSPP_SMP->amount,
            'discount_applied' => 0,
            'final_amount' => $fmSPP_SMP->amount,
            'status' => 'PAID',
            'created_at' => Carbon::parse('2026-02-01'),
        ]);

        Payment::create([
            'billing_id' => $billing2->id,
            'admin_id' => null,
            'amount' => $fmSPP_SMP->amount,
            'method' => 'duitku',
            'duitku_reference' => 'DUITKU-REF-20260210-001',
            'status' => 'paid',
            'paid_at' => Carbon::parse('2026-02-10'),
        ]);

        // ══════════════════════════════════════════════
        // STUDENT 1 (Ahmad) — SPP SMP Maret (UNPAID)
        // ══════════════════════════════════════════════
        Billing::create([
            'student_id' => $student1->id,
            'fee_master_id' => $fmSPP_SMP?->id,
            'title' => 'SPP SMP Maret 2026',
            'original_amount' => $fmSPP_SMP->amount,
            'discount_applied' => 0,
            'final_amount' => $fmSPP_SMP->amount,
            'status' => 'UNPAID',
            'created_at' => Carbon::parse('2026-03-01'),
        ]);

        // ══════════════════════════════════════════════
        // STUDENT 1 (Ahmad) — SPMB (PAID, cash)
        // ══════════════════════════════════════════════
        if ($fmSPMB) {
            $billingSPMB = Billing::create([
                'student_id' => $student1->id,
                'fee_master_id' => $fmSPMB->id,
                'title' => 'Biaya Pendaftaran SPMB',
                'original_amount' => $fmSPMB->amount,
                'discount_applied' => 0,
                'final_amount' => $fmSPMB->amount,
                'status' => 'PAID',
                'created_at' => Carbon::parse('2025-12-20'),
            ]);

            Payment::create([
                'billing_id' => $billingSPMB->id,
                'admin_id' => $admin->id,
                'amount' => $fmSPMB->amount,
                'method' => 'cash',
                'status' => 'paid',
                'paid_at' => Carbon::parse('2025-12-20'),
            ]);
        }

        // ══════════════════════════════════════════════
        // STUDENT 4 (Ridwan Jr, ANAK_GURU) — SPP SMA Januari (PAID, cash, dengan diskon)
        // ══════════════════════════════════════════════
        if ($student4 && $fmSPP_SMA) {
            $discountAmount = 3000; // ANAK_GURU discount
            $billing4 = Billing::create([
                'student_id' => $student4->id,
                'fee_master_id' => $fmSPP_SMA->id,
                'title' => 'SPP SMA Januari 2026',
                'original_amount' => $fmSPP_SMA->amount,
                'discount_applied' => $discountAmount,
                'final_amount' => $fmSPP_SMA->amount - $discountAmount,
                'status' => 'PAID',
                'created_at' => Carbon::parse('2026-01-05'),
            ]);

            Payment::create([
                'billing_id' => $billing4->id,
                'admin_id' => $admin->id,
                'amount' => $fmSPP_SMA->amount - $discountAmount,
                'method' => 'cash',
                'status' => 'paid',
                'paid_at' => Carbon::parse('2026-01-08'),
            ]);

            // SPP SMA Februari (UNPAID, ada payment PENDING via duitku)
            $billing4Feb = Billing::create([
                'student_id' => $student4->id,
                'fee_master_id' => $fmSPP_SMA->id,
                'title' => 'SPP SMA Februari 2026',
                'original_amount' => $fmSPP_SMA->amount,
                'discount_applied' => $discountAmount,
                'final_amount' => $fmSPP_SMA->amount - $discountAmount,
                'status' => 'UNPAID',
                'created_at' => Carbon::parse('2026-02-01'),
            ]);

            // Payment PENDING (duitku belum confirm)
            Payment::create([
                'billing_id' => $billing4Feb->id,
                'admin_id' => null,
                'amount' => $fmSPP_SMA->amount - $discountAmount,
                'method' => 'duitku',
                'duitku_reference' => 'DUITKU-REF-20260215-PENDING',
                'status' => 'pending',
                'paid_at' => Carbon::parse('2026-02-15'),
            ]);
        }

        // ══════════════════════════════════════════════
        // STUDENT 5 (Aisyah, PPTQ) — SPP PPTQ (UNPAID)
        // ══════════════════════════════════════════════
        if ($student5 && $fmSPP_PPTQ) {
            Billing::create([
                'student_id' => $student5->id,
                'fee_master_id' => $fmSPP_PPTQ->id,
                'title' => 'SPP PPTQ Januari 2026',
                'original_amount' => $fmSPP_PPTQ->amount,
                'discount_applied' => 0,
                'final_amount' => $fmSPP_PPTQ->amount,
                'status' => 'UNPAID',
                'created_at' => Carbon::parse('2026-01-05'),
            ]);
        }

        // ══════════════════════════════════════════════
        // STUDENT 6 (Zahra, YATIM) — SPP SMP + Asrama (UNPAID, dengan diskon YATIM)
        // ══════════════════════════════════════════════
        if ($student6) {
            $yatimDiscount = 2000;

            // SPP SMP Januari — diskon YATIM
            Billing::create([
                'student_id' => $student6->id,
                'fee_master_id' => $fmSPP_SMP->id,
                'title' => 'SPP SMP Januari 2026',
                'original_amount' => $fmSPP_SMP->amount,
                'discount_applied' => $yatimDiscount,
                'final_amount' => $fmSPP_SMP->amount - $yatimDiscount,
                'status' => 'UNPAID',
                'created_at' => Carbon::parse('2026-01-05'),
            ]);

            // Asrama Januari — diskon YATIM
            if ($fmAsrama) {
                Billing::create([
                    'student_id' => $student6->id,
                    'fee_master_id' => $fmAsrama->id,
                    'title' => 'Biaya Asrama Januari 2026',
                    'original_amount' => $fmAsrama->amount,
                    'discount_applied' => $yatimDiscount,
                    'final_amount' => $fmAsrama->amount - $yatimDiscount,
                    'status' => 'UNPAID',
                    'created_at' => Carbon::parse('2026-01-05'),
                ]);
            }

            // Daftar Ulang — EXPIRED
            if ($fmDaftarUlang) {
                Billing::create([
                    'student_id' => $student6->id,
                    'fee_master_id' => $fmDaftarUlang->id,
                    'title' => 'Biaya Daftar Ulang Semester Ganjil 2026',
                    'original_amount' => $fmDaftarUlang->amount,
                    'discount_applied' => 0,
                    'final_amount' => $fmDaftarUlang->amount,
                    'status' => 'EXPIRED',
                    'created_at' => Carbon::parse('2025-12-01'),
                ]);
            }

            // SPP SMP payment FAILED via duitku
            $billingFail = Billing::create([
                'student_id' => $student6->id,
                'fee_master_id' => $fmSPP_SMP->id,
                'title' => 'SPP SMP Februari 2026',
                'original_amount' => $fmSPP_SMP->amount,
                'discount_applied' => $yatimDiscount,
                'final_amount' => $fmSPP_SMP->amount - $yatimDiscount,
                'status' => 'UNPAID',
                'created_at' => Carbon::parse('2026-02-01'),
            ]);

            Payment::create([
                'billing_id' => $billingFail->id,
                'admin_id' => null,
                'amount' => $fmSPP_SMP->amount - $yatimDiscount,
                'method' => 'duitku',
                'duitku_reference' => 'DUITKU-REF-20260212-FAIL',
                'status' => 'failed',
                'paid_at' => Carbon::parse('2026-02-12'),
                'notes' => 'Pembayaran gagal: saldo tidak mencukupi.',
            ]);
        }

        // ══════════════════════════════════════════════
        // VOID billing — tagihan yang dibatalkan admin
        // ══════════════════════════════════════════════
        Billing::create([
            'student_id' => $student1->id,
            'fee_master_id' => $fmSPP_SMP?->id,
            'title' => 'SPP SMP Desember 2025 (VOID - salah input)',
            'original_amount' => $fmSPP_SMP->amount,
            'discount_applied' => 0,
            'final_amount' => $fmSPP_SMP->amount,
            'status' => 'VOID',
            'archive_reason' => 'Salah input tagihan, dibatalkan oleh admin.',
            'created_at' => Carbon::parse('2025-12-01'),
        ]);

        // ══════════════════════════════════════════════
        // PAID via duitku — Aisyah SPMB
        // ══════════════════════════════════════════════
        if ($student5 && $fmSPMB) {
            $billingAisyahSPMB = Billing::create([
                'student_id' => $student5->id,
                'fee_master_id' => $fmSPMB->id,
                'title' => 'Biaya Pendaftaran SPMB',
                'original_amount' => $fmSPMB->amount,
                'discount_applied' => 0,
                'final_amount' => $fmSPMB->amount,
                'status' => 'PAID',
                'created_at' => Carbon::parse('2025-12-15'),
            ]);

            Payment::create([
                'billing_id' => $billingAisyahSPMB->id,
                'admin_id' => null,
                'amount' => $fmSPMB->amount,
                'method' => 'duitku',
                'duitku_reference' => 'DUITKU-REF-20251215-002',
                'status' => 'paid',
                'paid_at' => Carbon::parse('2025-12-15'),
            ]);
        }
        }); // end Payment::withoutEvents
        }); // end Billing::withoutEvents
    }
}
