<?php

namespace Database\Seeders;

use App\Models\Billing;
use App\Models\Discount;
use App\Models\FeeMaster;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BillingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Membuat billing yang merepresentasikan semua status:
     * - PAID   : sudah dibayar (cash & duitku)
     * - UNPAID : belum dibayar
     * - EXPIRED: sudah kedaluwarsa
     * - VOID   : dibatalkan
     *
     * Payment statuses yang dicover:
     * - paid    : pembayaran sukses
     * - pending : menunggu pembayaran duitku
     * - failed  : pembayaran gagal
     */
    public function run(): void
    {
        $admin = User::where('role', 'SUPER_ADMIN')->first();

        if (!$admin) {
            $this->command->info('No admin found. Please run UserSeeder first.');
            return;
        }

        // Ambil fee masters untuk referensi
        $fmSPP_SMP  = FeeMaster::where('item_name', 'SPP SMP')->first();
        $fmSPP_SMA  = FeeMaster::where('item_name', 'SPP SMA')->first();
        $fmSPP_PPTQ = FeeMaster::where('item_name', 'SPP PPTQ')->first();
        $fmAsrama   = FeeMaster::where('item_name', 'Biaya Asrama Bulanan')->first();
        $fmPocket   = FeeMaster::where('item_name', 'Uang Saku Bulanan')->first();
        $fmReg      = FeeMaster::where('item_name', 'Biaya Pendaftaran SPMB')->first();
        $fmSemester = FeeMaster::where('item_name', 'Biaya Semester')->first();

        // ──────────────────────────────────────────────
        // Santri 1 — Ahmad Santri (SMP, MONDOK, UMUM, diterima)
        // Billing: PAID (cash), UNPAID, EXPIRED
        // ──────────────────────────────────────────────
        $student1 = Student::where('nis', '2026.01.0001')->first();
        if ($student1) {
            // PAID — SPP Januari, bayar CASH
            $billing1 = Billing::create([
                'student_id' => $student1->id,
                'fee_master_id' => $fmSPP_SMP?->id,
                'title' => 'SPP SMP - Januari 2026',
                'original_amount' => 12000,
                'discount_applied' => 0,
                'final_amount' => 12000,
                'status' => 'PAID',
                'billing_period_start' => '2026-01-01',
                'billing_period_end' => '2026-01-31',
                'created_at' => Carbon::parse('2026-01-10'),
                'updated_at' => Carbon::parse('2026-01-15'),
            ]);

            Payment::create([
                'billing_id' => $billing1->id,
                'admin_id' => $admin->id,
                'amount' => 12000,
                'method' => 'CASH',
                'status' => 'paid',
                'paid_at' => Carbon::parse('2026-01-15'),
                'snapshot_billing_amount' => 12000,
            ]);

            // UNPAID — SPP Februari
            Billing::create([
                'student_id' => $student1->id,
                'fee_master_id' => $fmSPP_SMP?->id,
                'title' => 'SPP SMP - Februari 2026',
                'original_amount' => 12000,
                'discount_applied' => 0,
                'final_amount' => 12000,
                'status' => 'UNPAID',
                'billing_period_start' => '2026-02-01',
                'billing_period_end' => '2026-02-28',
                'created_at' => Carbon::parse('2026-02-10'),
                'updated_at' => Carbon::parse('2026-02-10'),
            ]);

            // EXPIRED — Pendaftaran yang sudah lewat
            Billing::create([
                'student_id' => $student1->id,
                'fee_master_id' => $fmReg?->id,
                'title' => 'Biaya Pendaftaran SPMB',
                'original_amount' => 10000,
                'discount_applied' => 0,
                'final_amount' => 10000,
                'status' => 'EXPIRED',
                'expires_at' => Carbon::parse('2025-12-31'),
                'created_at' => Carbon::parse('2025-11-01'),
                'updated_at' => Carbon::parse('2025-12-31'),
            ]);

            // UNPAID — Asrama Februari (MONDOK fee)
            Billing::create([
                'student_id' => $student1->id,
                'fee_master_id' => $fmAsrama?->id,
                'title' => 'Biaya Asrama - Februari 2026',
                'original_amount' => 14000,
                'discount_applied' => 0,
                'final_amount' => 14000,
                'status' => 'UNPAID',
                'billing_period_start' => '2026-02-01',
                'billing_period_end' => '2026-02-28',
                'created_at' => Carbon::parse('2026-02-10'),
                'updated_at' => Carbon::parse('2026-02-10'),
            ]);

            // PAID — Uang Saku Januari (MONDOK fee)
            $billingPocket = Billing::create([
                'student_id' => $student1->id,
                'fee_master_id' => $fmPocket?->id,
                'title' => 'Uang Saku - Januari 2026',
                'original_amount' => 10000,
                'discount_applied' => 0,
                'final_amount' => 10000,
                'status' => 'PAID',
                'billing_period_start' => '2026-01-01',
                'billing_period_end' => '2026-01-31',
                'created_at' => Carbon::parse('2026-01-05'),
                'updated_at' => Carbon::parse('2026-01-08'),
            ]);

            Payment::create([
                'billing_id' => $billingPocket->id,
                'admin_id' => $admin->id,
                'amount' => 10000,
                'method' => 'CASH',
                'status' => 'paid',
                'paid_at' => Carbon::parse('2026-01-08'),
                'snapshot_billing_amount' => 10000,
            ]);
        }

        // ──────────────────────────────────────────────
        // Santri 4 — Muhammad Ridwan Jr (SMA, MONDOK, ANAK_GURU, diterima)
        // Billing: PAID (duitku), VOID, UNPAID dengan diskon
        // ──────────────────────────────────────────────
        $student4 = Student::where('nis', '2026.02.0002')->first();
        if ($student4) {
            // PAID — SPP Januari via Duitku
            $billing4 = Billing::create([
                'student_id' => $student4->id,
                'fee_master_id' => $fmSPP_SMA?->id,
                'title' => 'SPP SMA - Januari 2026',
                'original_amount' => 13000,
                'discount_applied' => 0,
                'final_amount' => 13000,
                'status' => 'PAID',
                'billing_period_start' => '2026-01-01',
                'billing_period_end' => '2026-01-31',
                'created_at' => Carbon::parse('2026-01-10'),
                'updated_at' => Carbon::parse('2026-01-12'),
            ]);

            Payment::create([
                'billing_id' => $billing4->id,
                'admin_id' => null,
                'amount' => 13000,
                'method' => 'duitku',
                'duitku_reference' => 'DUITKU-REF-20260112-001',
                'status' => 'paid',
                'paid_at' => Carbon::parse('2026-01-12'),
                'snapshot_billing_amount' => 13000,
            ]);

            // VOID — tagihan yang dibatalkan
            Billing::create([
                'student_id' => $student4->id,
                'fee_master_id' => $fmSemester?->id,
                'title' => 'Biaya Semester (Void)',
                'original_amount' => 15000,
                'discount_applied' => 0,
                'final_amount' => 15000,
                'status' => 'VOID',
                'archive_reason' => 'Tagihan duplikat, dibatalkan oleh admin.',
                'archived_by' => $admin->id,
                'archived_at' => Carbon::parse('2026-01-20'),
                'created_at' => Carbon::parse('2026-01-15'),
                'updated_at' => Carbon::parse('2026-01-20'),
            ]);

            // UNPAID — SPP Februari
            Billing::create([
                'student_id' => $student4->id,
                'fee_master_id' => $fmSPP_SMA?->id,
                'title' => 'SPP SMA - Februari 2026',
                'original_amount' => 13000,
                'discount_applied' => 0,
                'final_amount' => 13000,
                'status' => 'UNPAID',
                'billing_period_start' => '2026-02-01',
                'billing_period_end' => '2026-02-28',
                'created_at' => Carbon::parse('2026-02-10'),
                'updated_at' => Carbon::parse('2026-02-10'),
            ]);

            // UNPAID — Asrama (MONDOK), dengan payment pending (duitku)
            $billingPendingPayment = Billing::create([
                'student_id' => $student4->id,
                'fee_master_id' => $fmAsrama?->id,
                'title' => 'Biaya Asrama - Februari 2026',
                'original_amount' => 14000,
                'discount_applied' => 0,
                'final_amount' => 14000,
                'status' => 'UNPAID',
                'billing_period_start' => '2026-02-01',
                'billing_period_end' => '2026-02-28',
                'created_at' => Carbon::parse('2026-02-10'),
                'updated_at' => Carbon::parse('2026-02-10'),
            ]);

            // Payment PENDING (duitku belum confirm)
            Payment::create([
                'billing_id' => $billingPendingPayment->id,
                'admin_id' => null,
                'amount' => 14000,
                'method' => 'duitku',
                'duitku_reference' => 'DUITKU-REF-20260215-PENDING',
                'status' => 'pending',
                'paid_at' => Carbon::parse('2026-02-15'),
                'snapshot_billing_amount' => 14000,
            ]);
        }

        // ──────────────────────────────────────────────
        // Santri 5 — Aisyah binti Ridwan (PPTQ, MONDOK, UMUM, diterima)
        // Billing: UNPAID, PAID
        // ──────────────────────────────────────────────
        $student5 = Student::where('nis', '2026.03.0001')->first();
        if ($student5) {
            // PAID — SPP PPTQ Januari
            $billing5 = Billing::create([
                'student_id' => $student5->id,
                'fee_master_id' => $fmSPP_PPTQ?->id,
                'title' => 'SPP PPTQ - Januari 2026',
                'original_amount' => 11000,
                'discount_applied' => 0,
                'final_amount' => 11000,
                'status' => 'PAID',
                'billing_period_start' => '2026-01-01',
                'billing_period_end' => '2026-01-31',
                'created_at' => Carbon::parse('2026-01-10'),
                'updated_at' => Carbon::parse('2026-01-18'),
            ]);

            Payment::create([
                'billing_id' => $billing5->id,
                'admin_id' => $admin->id,
                'amount' => 11000,
                'method' => 'CASH',
                'status' => 'paid',
                'paid_at' => Carbon::parse('2026-01-18'),
                'snapshot_billing_amount' => 11000,
            ]);

            // UNPAID — SPP PPTQ Februari
            Billing::create([
                'student_id' => $student5->id,
                'fee_master_id' => $fmSPP_PPTQ?->id,
                'title' => 'SPP PPTQ - Februari 2026',
                'original_amount' => 11000,
                'discount_applied' => 0,
                'final_amount' => 11000,
                'status' => 'UNPAID',
                'billing_period_start' => '2026-02-01',
                'billing_period_end' => '2026-02-28',
                'created_at' => Carbon::parse('2026-02-10'),
                'updated_at' => Carbon::parse('2026-02-10'),
            ]);
        }

        // ──────────────────────────────────────────────
        // Santri 6 — Zahra binti Rahmah (SMP, NGAJI_ONLY, YATIM, diterima)
        // Billing: PAID dengan diskon YATIM, UNPAID
        // Payment: FAILED demo
        // ──────────────────────────────────────────────
        $student6 = Student::where('nis', '2026.01.0003')->first();
        if ($student6) {
            // PAID — SPP Januari (dengan diskon YATIM)
            $billing6 = Billing::create([
                'student_id' => $student6->id,
                'fee_master_id' => $fmSPP_SMP?->id,
                'title' => 'SPP SMP - Januari 2026',
                'original_amount' => 12000,
                'discount_applied' => 2000,
                'final_amount' => 10000,
                'status' => 'PAID',
                'billing_period_start' => '2026-01-01',
                'billing_period_end' => '2026-01-31',
                'created_at' => Carbon::parse('2026-01-10'),
                'updated_at' => Carbon::parse('2026-01-20'),
            ]);

            Payment::create([
                'billing_id' => $billing6->id,
                'admin_id' => $admin->id,
                'amount' => 10000,
                'method' => 'CASH',
                'status' => 'paid',
                'paid_at' => Carbon::parse('2026-01-20'),
                'snapshot_billing_amount' => 10000,
            ]);

            // UNPAID — SPP Februari (dengan diskon YATIM)
            $billing6Feb = Billing::create([
                'student_id' => $student6->id,
                'fee_master_id' => $fmSPP_SMP?->id,
                'title' => 'SPP SMP - Februari 2026',
                'original_amount' => 12000,
                'discount_applied' => 2000,
                'final_amount' => 10000,
                'status' => 'UNPAID',
                'billing_period_start' => '2026-02-01',
                'billing_period_end' => '2026-02-28',
                'created_at' => Carbon::parse('2026-02-10'),
                'updated_at' => Carbon::parse('2026-02-10'),
            ]);

            // Payment FAILED — pembayaran duitku yang gagal
            Payment::create([
                'billing_id' => $billing6Feb->id,
                'admin_id' => null,
                'amount' => 10000,
                'method' => 'duitku',
                'duitku_reference' => 'DUITKU-REF-20260212-FAIL',
                'status' => 'failed',
                'paid_at' => Carbon::parse('2026-02-12'),
                'snapshot_billing_amount' => 10000,
                'notes' => 'Pembayaran gagal: saldo tidak mencukupi.',
            ]);
        }
    }
}
