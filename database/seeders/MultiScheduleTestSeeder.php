<?php

namespace Database\Seeders;

use App\Enums\StudentStatus;
use App\Models\Guardian;
use App\Models\SpmbSchedule;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder untuk test data multi jadwal SPMB + multi santri pending.
 * Berguna untuk preview halaman "Penerimaan Santri" dengan beberapa
 * gelombang/pembagian jadwal dan banyak santri menunggu konfirmasi.
 *
 * Jalankan: php artisan db:seed --class=MultiScheduleTestSeeder
 */
class MultiScheduleTestSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────────
        // 1. Buat additional SPMB schedules (aktif)
        // ──────────────────────────────────────────────
        $currentYear = now()->year;
        $nextYear = $currentYear + 1;

        $scheduleGelombang1 = SpmbSchedule::create([
            'name' => "SPMB Gelombang 1 T.A. {$currentYear}/{$nextYear}",
            'description' => 'Gelombang pertama pendaftaran santri baru tahun ajaran ' . $currentYear . '/' . $nextYear,
            'registration_start' => now()->subDays(15),
            'registration_end' => now()->addDays(20),
            'is_active' => true,
        ]);

        $scheduleGelombang2 = SpmbSchedule::create([
            'name' => "SPMB Gelombang 2 T.A. {$currentYear}/{$nextYear}",
            'description' => 'Gelombang kedua pendaftaran santri baru tahun ajaran ' . $currentYear . '/' . $nextYear,
            'registration_start' => now()->subDays(3),
            'registration_end' => now()->addDays(30),
            'is_active' => true,
        ]);

        $scheduleKhusus = SpmbSchedule::create([
            'name' => "SPMB Khusus / Mutasi T.A. {$currentYear}/{$nextYear}",
            'description' => 'Pendaftaran khusus untuk santri mutasi/pindahan',
            'registration_start' => now()->subDays(1),
            'registration_end' => now()->addDays(15),
            'is_active' => true,
        ]);

        // ──────────────────────────────────────────────
        // 2. Buat guardians + students per schedule
        // ──────────────────────────────────────────────

        // ═══════════════════════════════════════════════
        // GELombang 1: 3 santri pending
        // ═══════════════════════════════════════════════

        // Wali G1-1: H. Ahmad Fauzi — 2 santri
        $userG1_1 = User::create([
            'name' => 'H. Ahmad Fauzi',
            'whatsapp' => '081200000001',
            'email' => null,
            'password' => Hash::make('password'),
            'role' => 'WALI_SANTRI',
        ]);
        $guardianG1_1 = Guardian::create([
            'user_id' => $userG1_1->id,
            'full_name' => 'H. Ahmad Fauzi',
            'whatsapp' => '081200000001',
        ]);

        // Pending — SMP, MONDOK, UMUM
        Student::create([
            'guardian_id' => $guardianG1_1->id,
            'spmb_schedule_id' => $scheduleGelombang1->id,
            'registration_number' => '2026.G1.001',
            'full_name' => 'Muhammad Rizky Pratama',
            'unit_code' => '01',
            'residence_status' => 'MONDOK',
            'special_status' => 'UMUM',
            'status' => StudentStatus::PENDING->value,
            'is_active' => false,
            'address' => 'Jl. Mawar No. 12, Kab. Cirebon',
        ]);

        // Pending — SMA, NON_MONDOK, UMUM
        Student::create([
            'guardian_id' => $guardianG1_1->id,
            'spmb_schedule_id' => $scheduleGelombang1->id,
            'registration_number' => '2026.G1.002',
            'full_name' => 'Aisyah Putri Fauzi',
            'unit_code' => '02',
            'residence_status' => 'NON_MONDOK',
            'special_status' => 'UMUM',
            'status' => StudentStatus::PENDING->value,
            'is_active' => false,
            'address' => 'Jl. Mawar No. 12, Kab. Cirebon',
        ]);

        // Wali G1-2: Ibu Nurul Hidayah — 1 santri
        $userG1_2 = User::create([
            'name' => 'Ibu Nurul Hidayah',
            'whatsapp' => '081200000002',
            'email' => null,
            'password' => Hash::make('password'),
            'role' => 'WALI_SANTRI',
        ]);
        $guardianG1_2 = Guardian::create([
            'user_id' => $userG1_2->id,
            'full_name' => 'Ibu Nurul Hidayah',
            'whatsapp' => '081200000002',
        ]);

        // Pending — PPTQ, MONDOK, YATIM
        Student::create([
            'guardian_id' => $guardianG1_2->id,
            'spmb_schedule_id' => $scheduleGelombang1->id,
            'registration_number' => '2026.G1.003',
            'full_name' => 'Abdullah bin Hafizh',
            'unit_code' => '03',
            'residence_status' => 'MONDOK',
            'special_status' => 'YATIM',
            'status' => StudentStatus::PENDING->value,
            'is_active' => false,
            'address' => 'Jl. Pahlawan No. 8, Kota Tasikmalaya',
        ]);

        // ═══════════════════════════════════════════════
        // GELombang 2: 3 santri pending
        // ═══════════════════════════════════════════════

        // Wali G2-1: Bapak Muhammad Saleh — 2 santri
        $userG2_1 = User::create([
            'name' => 'Bapak Muhammad Saleh',
            'whatsapp' => '081200000003',
            'email' => null,
            'password' => Hash::make('password'),
            'role' => 'WALI_SANTRI',
        ]);
        $guardianG2_1 = Guardian::create([
            'user_id' => $userG2_1->id,
            'full_name' => 'H. Muhammad Saleh',
            'whatsapp' => '081200000003',
        ]);

        // Pending — SMP, MONDOK, ANAK_GURU
        Student::create([
            'guardian_id' => $guardianG2_1->id,
            'spmb_schedule_id' => $scheduleGelombang2->id,
            'registration_number' => '2026.G2.001',
            'full_name' => 'Hafizh Ahmad Saleh',
            'unit_code' => '01',
            'residence_status' => 'MONDOK',
            'special_status' => 'ANAK_GURU',
            'status' => StudentStatus::PENDING->value,
            'is_active' => false,
            'address' => 'Jl. Kenanga No. 20, Kota Bandung',
        ]);

        // Pending — SMA, NON_MONDOK, UMUM
        Student::create([
            'guardian_id' => $guardianG2_1->id,
            'spmb_schedule_id' => $scheduleGelombang2->id,
            'registration_number' => '2026.G2.002',
            'full_name' => 'Salma Azzahra Saleh',
            'unit_code' => '02',
            'residence_status' => 'NON_MONDOK',
            'special_status' => 'UMUM',
            'status' => StudentStatus::PENDING->value,
            'is_active' => false,
            'address' => 'Jl. Kenanga No. 20, Kota Bandung',
        ]);

        // Wali G2-2: Ibu Fatimah — 1 santri
        $userG2_2 = User::create([
            'name' => 'Ibu Fatimah Azzahra',
            'whatsapp' => '081200000004',
            'email' => null,
            'password' => Hash::make('password'),
            'role' => 'WALI_SANTRI',
        ]);
        $guardianG2_2 = Guardian::create([
            'user_id' => $userG2_2->id,
            'full_name' => 'Ibu Fatimah Azzahra',
            'whatsapp' => '081200000004',
        ]);

        // Pending — PPTQ, NGAJI_ONLY, UMUM
        Student::create([
            'guardian_id' => $guardianG2_2->id,
            'spmb_schedule_id' => $scheduleGelombang2->id,
            'registration_number' => '2026.G2.003',
            'full_name' => 'Yusuf Maulana',
            'unit_code' => '03',
            'residence_status' => 'NGAJI_ONLY',
            'special_status' => 'UMUM',
            'status' => StudentStatus::PENDING->value,
            'is_active' => false,
            'address' => 'Jl. Melati No. 5, Kab. Garut',
        ]);

        // ═══════════════════════════════════════════════
        // SPMB Khusus / Mutasi: 2 santri pending
        // ═══════════════════════════════════════════════

        // Wali K-1: Bapak Ibrahim — 2 santri
        $userK_1 = User::create([
            'name' => 'Bapak Ibrahim Khusus',
            'whatsapp' => '081200000005',
            'email' => null,
            'password' => Hash::make('password'),
            'role' => 'WALI_SANTRI',
        ]);
        $guardianK_1 = Guardian::create([
            'user_id' => $userK_1->id,
            'full_name' => 'H. Ibrahim Mansur',
            'whatsapp' => '081200000005',
        ]);

        // Pending — SMP, MONDOK, UMUM (santri mutasi)
        Student::create([
            'guardian_id' => $guardianK_1->id,
            'spmb_schedule_id' => $scheduleKhusus->id,
            'registration_number' => '2026.KH.001',
            'full_name' => 'Thariq bin Ibrahim',
            'unit_code' => '01',
            'residence_status' => 'MONDOK',
            'special_status' => 'UMUM',
            'status' => StudentStatus::PENDING->value,
            'is_active' => false,
            'address' => 'Jl. Diponegoro No. 33, Kota Sukabumi',
        ]);

        // Pending — SMA, MONDOK, YATIM (santri mutasi)
        Student::create([
            'guardian_id' => $guardianK_1->id,
            'spmb_schedule_id' => $scheduleKhusus->id,
            'registration_number' => '2026.KH.002',
            'full_name' => 'Naura Aulia Ibrahim',
            'unit_code' => '02',
            'residence_status' => 'MONDOK',
            'special_status' => 'YATIM',
            'status' => StudentStatus::PENDING->value,
            'is_active' => false,
            'address' => 'Jl. Diponegoro No. 33, Kota Sukabumi',
        ]);

        $this->command->info("✅ Multi-schedule test data seeder completed!");
        $this->command->info("   - 3 schedules created (Gelombang 1, Gelombang 2, Khusus/Mutasi)");
        $this->command->info("   - 8 pending students across different schedules");
        $this->command->info("   - Plus 2 existing pending (Fatima & Ibrahim) on original schedule");
    }
}
