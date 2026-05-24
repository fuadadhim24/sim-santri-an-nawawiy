<?php

namespace Database\Seeders;

use App\Enums\StudentStatus;
use App\Models\Guardian;
use App\Models\SpmbSchedule;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────────
        // 1. Admin accounts
        // ──────────────────────────────────────────────
        User::create([
            'name' => 'Super Admin',
            'whatsapp' => '080000000001',
            'email' => 'admin@annawawiy.ac.id',
            'password' => Hash::make('password'),
            'role' => 'SUPER_ADMIN',
        ]);

        User::create([
            'name' => 'Administrasi',
            'whatsapp' => '080000000002',
            'email' => 'administrasi@annawawiy.ac.id',
            'password' => Hash::make('password'),
            'role' => 'ADMINISTRASI',
        ]);

        User::create([
            'name' => 'Bendahara',
            'whatsapp' => '080000000003',
            'email' => 'bendahara@annawawiy.ac.id',
            'password' => Hash::make('password'),
            'role' => 'BENDAHARA',
        ]);

        $spmbSchedule = SpmbSchedule::where('is_active', true)->first();

        $level7 = \App\Models\ClassLevel::where('name', 'Kelas 7 SMP')->first();
        $rombel7A = \App\Models\StudyGroup::where('name', 'Kelas 7 SMP - A')->first();
        
        $level8 = \App\Models\ClassLevel::where('name', 'Kelas 8 SMP')->first();
        $rombel8B = \App\Models\StudyGroup::where('name', 'Kelas 8 SMP - B')->first();

        $level10 = \App\Models\ClassLevel::where('name', 'Kelas 10 SMA')->first();
        $rombel10A = \App\Models\StudyGroup::where('name', 'Kelas 10 SMA - A')->first();

        $levelTahfidz = \App\Models\ClassLevel::where('name', 'Kelas Tahfidz')->first();
        $rombelTahfidzA = \App\Models\StudyGroup::where('name', 'Kelas Tahfidz - A')->first();

        // ──────────────────────────────────────────────
        // 2. Wali Santri 1 — H. Abdullah (2 santri: diterima + ditolak)
        // ──────────────────────────────────────────────
        $waliUser1 = User::create([
            'name' => 'H. Abdullah',
            'whatsapp' => '081234567890',
            'email' => null,
            'password' => Hash::make('password'),
            'role' => 'WALI_SANTRI',
        ]);

        $guardian1 = Guardian::create([
            'user_id' => $waliUser1->id,
            'full_name' => 'H. Abdullah',
            'whatsapp' => '081234567890',
        ]);

        // Santri 1: SMP, MONDOK, UMUM, status=diterima (aktif)
        Student::create([
            'guardian_id' => $guardian1->id,
            'spmb_schedule_id' => $spmbSchedule?->id,
            'nis' => '2026.01.0001',
            'full_name' => 'Ahmad Santri',
            'unit_code' => '01',
            'residence_status' => 'MONDOK',
            'special_status' => 'UMUM',
            'status' => StudentStatus::ACCEPTED->value,
            'class_level_id' => $level7?->id,
            'study_group_id' => $rombel7A?->id,
            'address' => 'Jl. Pendidikan No. 1, Kab. Bogor',
            'is_active' => true,
        ]);

        // Santri 2: SMA, MONDOK, YATIM, status=ditolak
        Student::create([
            'guardian_id' => $guardian1->id,
            'spmb_schedule_id' => $spmbSchedule?->id,
            'nis' => '2026.02.0001',
            'full_name' => 'Yusuf bin Abdullah',
            'unit_code' => '02',
            'residence_status' => 'MONDOK',
            'special_status' => 'YATIM',
            'status' => StudentStatus::REJECTED->value,
            'class_level_id' => null,
            'study_group_id' => null,
            'address' => 'Jl. Pendidikan No. 1, Kab. Bogor',
            'is_active' => false,
        ]);

        // ──────────────────────────────────────────────
        // 3. Wali Santri 2 — Ibu Siti (1 santri: menunggu)
        // ──────────────────────────────────────────────
        $waliUser2 = User::create([
            'name' => 'Ibu Siti',
            'whatsapp' => '082345678901',
            'email' => null,
            'password' => Hash::make('password'),
            'role' => 'WALI_SANTRI',
        ]);

        $guardian2 = Guardian::create([
            'user_id' => $waliUser2->id,
            'full_name' => 'Ibu Siti Mariyam',
            'whatsapp' => '082345678901',
        ]);

        // Santri 3: SMP, NON_MONDOK, UMUM, status=menunggu (pending SPMB)
        Student::create([
            'guardian_id' => $guardian2->id,
            'spmb_schedule_id' => $spmbSchedule?->id,
            'nis' => '2026.01.0002',
            'full_name' => 'Fatima Binti Usman',
            'unit_code' => '01',
            'residence_status' => 'NON_MONDOK',
            'special_status' => 'UMUM',
            'status' => StudentStatus::PENDING->value,
            'class_level_id' => null,
            'study_group_id' => null,
            'address' => 'Jl. Pesantren No. 5, Kab. Bogor',
            'is_active' => false,
        ]);

        // ──────────────────────────────────────────────
        // 4. Wali Santri 3 — Bapak Ridwan (2 santri: diterima di SMA + diterima di PPTQ)
        // ──────────────────────────────────────────────
        $waliUser3 = User::create([
            'name' => 'Bapak Ridwan',
            'whatsapp' => '085678901234',
            'email' => null,
            'password' => Hash::make('password'),
            'role' => 'WALI_SANTRI',
        ]);

        $guardian3 = Guardian::create([
            'user_id' => $waliUser3->id,
            'full_name' => 'H. Ridwan Kamil',
            'whatsapp' => '085678901234',
        ]);

        // Santri 4: SMA, MONDOK, ANAK_GURU, status=diterima
        Student::create([
            'guardian_id' => $guardian3->id,
            'spmb_schedule_id' => $spmbSchedule?->id,
            'nis' => '2026.02.0002',
            'full_name' => 'Muhammad Ridwan Jr',
            'unit_code' => '02',
            'residence_status' => 'MONDOK',
            'special_status' => 'ANAK_GURU',
            'status' => StudentStatus::ACCEPTED->value,
            'class_level_id' => $level10?->id,
            'study_group_id' => $rombel10A?->id,
            'address' => 'Jl. Ulama No. 10, Kota Bandung',
            'is_active' => true,
        ]);

        // Santri 5: PPTQ, MONDOK, UMUM, status=diterima
        Student::create([
            'guardian_id' => $guardian3->id,
            'spmb_schedule_id' => $spmbSchedule?->id,
            'nis' => '2026.03.0001',
            'full_name' => 'Aisyah binti Ridwan',
            'unit_code' => '03',
            'residence_status' => 'MONDOK',
            'special_status' => 'UMUM',
            'status' => StudentStatus::ACCEPTED->value,
            'class_level_id' => $levelTahfidz?->id,
            'study_group_id' => $rombelTahfidzA?->id,
            'address' => 'Jl. Ulama No. 10, Kota Bandung',
            'is_active' => true,
        ]);

        // ──────────────────────────────────────────────
        // 5. Wali Santri 4 — Ibu Rahmah (1 santri: NGAJI_ONLY, YATIM, diterima)
        // ──────────────────────────────────────────────
        $waliUser4 = User::create([
            'name' => 'Ibu Rahmah',
            'whatsapp' => '087890123456',
            'email' => null,
            'password' => Hash::make('password'),
            'role' => 'WALI_SANTRI',
        ]);

        $guardian4 = Guardian::create([
            'user_id' => $waliUser4->id,
            'full_name' => 'Ibu Rahmah Azzahra',
            'whatsapp' => '087890123456',
        ]);

        // Santri 6: SMP, NGAJI_ONLY, YATIM, status=diterima
        Student::create([
            'guardian_id' => $guardian4->id,
            'spmb_schedule_id' => $spmbSchedule?->id,
            'nis' => '2026.01.0003',
            'full_name' => 'Zahra binti Rahmah',
            'unit_code' => '01',
            'residence_status' => 'NGAJI_ONLY',
            'special_status' => 'YATIM',
            'status' => StudentStatus::ACCEPTED->value,
            'class_level_id' => $level8?->id,
            'study_group_id' => $rombel8B?->id,
            'address' => 'Jl. Bahagia No. 3, Kab. Bogor',
            'is_active' => true,
        ]);

        // ──────────────────────────────────────────────
        // 6. Wali Santri 5 — Bapak Hasan (1 santri: menunggu, NON_MONDOK) + 1 Santri LULUS
        // ──────────────────────────────────────────────
        $waliUser5 = User::create([
            'name' => 'Bapak Hasan',
            'whatsapp' => '089012345678',
            'email' => null,
            'password' => Hash::make('password'),
            'role' => 'WALI_SANTRI',
        ]);

        $guardian5 = Guardian::create([
            'user_id' => $waliUser5->id,
            'full_name' => 'H. Hasan Basri',
            'whatsapp' => '089012345678',
        ]);

        // Santri 7: SMA, NON_MONDOK, ANAK_GURU, status=menunggu
        Student::create([
            'guardian_id' => $guardian5->id,
            'spmb_schedule_id' => $spmbSchedule?->id,
            'nis' => '2026.02.0003',
            'full_name' => 'Ibrahim bin Hasan',
            'unit_code' => '02',
            'residence_status' => 'NON_MONDOK',
            'special_status' => 'ANAK_GURU',
            'status' => StudentStatus::PENDING->value,
            'class_level_id' => null,
            'study_group_id' => null,
            'address' => 'Jl. Merdeka No. 7, Kota Bogor',
            'is_active' => false,
        ]);

        // Santri 8: LULUS ALUMNI
        Student::create([
            'guardian_id' => $guardian5->id,
            'spmb_schedule_id' => $spmbSchedule?->id,
            'nis' => '2023.01.0009',
            'full_name' => 'Fathurrahman (Alumni)',
            'unit_code' => '01',
            'residence_status' => 'MONDOK',
            'special_status' => 'UMUM',
            'status' => 'lulus',
            'class_level_id' => null,
            'study_group_id' => null,
            'address' => 'Jl. Pahlawan No. 9, Kota Bogor',
            'is_active' => false,
        ]);
    }
}
