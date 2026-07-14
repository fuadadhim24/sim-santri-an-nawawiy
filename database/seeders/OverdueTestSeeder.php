<?php

namespace Database\Seeders;

use App\Enums\StudentStatus;
use App\Models\Billing;
use App\Models\FeeMaster;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder khusus untuk testing tampilan modal pengingat WA
 * dengan ~30 tagihan terlambat dari 20 santri berbeda.
 */
class OverdueTestSeeder extends Seeder
{
    public function run(): void
    {
        Billing::withoutEvents(function () {

        $spmbSchedule = \App\Models\SpmbSchedule::where('is_active', true)->first();

        // Rombel references
        $level7      = \App\Models\ClassLevel::where('name', 'Kelas 7 SMP')->first();
        $rombel7A    = \App\Models\StudyGroup::where('name', 'Kelas 7 SMP - A')->first();
        $level8      = \App\Models\ClassLevel::where('name', 'Kelas 8 SMP')->first();
        $rombel8B    = \App\Models\StudyGroup::where('name', 'Kelas 8 SMP - B')->first();
        $level10     = \App\Models\ClassLevel::where('name', 'Kelas 10 SMA')->first();
        $rombel10A   = \App\Models\StudyGroup::where('name', 'Kelas 10 SMA - A')->first();
        $levelTahfidz   = \App\Models\ClassLevel::where('name', 'Kelas Tahfidz')->first();
        $rombelTahfidzA = \App\Models\StudyGroup::where('name', 'Kelas Tahfidz - A')->first();

        // Fee Masters
        $fmSPP_SMP  = FeeMaster::where('item_name', 'SPP SMP')->first();
        $fmSPP_SMA  = FeeMaster::where('item_name', 'SPP SMA')->first();
        $fmSPP_PPTQ = FeeMaster::where('item_name', 'SPP PPTQ')->first();
        $fmAsrama   = FeeMaster::where('item_name', 'Biaya Asrama Bulanan')->first();

        if (!$fmSPP_SMP || !$fmSPP_SMA || !$fmSPP_PPTQ) {
            $this->command->info('Skipping OverdueTestSeeder: FeeMaster not found.');
            return;
        }

        // ──────────────────────────────────────────────
        // Data 12 wali + santri baru
        // ──────────────────────────────────────────────
        $newStudents = [
            ['wali' => 'Bapak Zainuddin',  'wa' => '081300000001', 'santri' => 'Zaki bin Zainuddin',       'nis' => '2627.01.0020', 'reg' => '2026.0020', 'unit' => '01', 'res' => 'MONDOK',     'special' => 'UMUM',  'level' => $level7,       'rombel' => $rombel7A],
            ['wali' => 'Ibu Nurhaliza',    'wa' => '081300000002', 'santri' => 'Nabila binti Nurhaliza',    'nis' => '2627.02.0020', 'reg' => '2026.0021', 'unit' => '02', 'res' => 'MONDOK',     'special' => 'UMUM',  'level' => $level10,      'rombel' => $rombel10A],
            ['wali' => 'Bapak Sulaiman',   'wa' => '081300000003', 'santri' => 'Salman bin Sulaiman',       'nis' => '2627.03.0020', 'reg' => '2026.0022', 'unit' => '03', 'res' => 'MONDOK',     'special' => 'UMUM',  'level' => $levelTahfidz, 'rombel' => $rombelTahfidzA],
            ['wali' => 'Ibu Khadijah',     'wa' => '081300000004', 'santri' => 'Hamzah bin Khadijah',       'nis' => '2627.01.0021', 'reg' => '2026.0023', 'unit' => '01', 'res' => 'NON_MONDOK', 'special' => 'YATIM', 'level' => $level8,       'rombel' => $rombel8B],
            ['wali' => 'Bapak Umar',       'wa' => '081300000005', 'santri' => 'Usman bin Umar',            'nis' => '2627.02.0021', 'reg' => '2026.0024', 'unit' => '02', 'res' => 'MONDOK',     'special' => 'UMUM',  'level' => $level10,      'rombel' => $rombel10A],
            ['wali' => 'Ibu Aisyah',       'wa' => '081300000006', 'santri' => 'Ruqayyah binti Aisyah',     'nis' => '2627.01.0022', 'reg' => '2026.0025', 'unit' => '01', 'res' => 'MONDOK',     'special' => 'UMUM',  'level' => $level7,       'rombel' => $rombel7A],
            ['wali' => 'Bapak Bilal',      'wa' => '081300000007', 'santri' => 'Bilal Jr bin Bilal',        'nis' => '2627.03.0021', 'reg' => '2026.0026', 'unit' => '03', 'res' => 'MONDOK',     'special' => 'UMUM',  'level' => $levelTahfidz, 'rombel' => $rombelTahfidzA],
            ['wali' => 'Ibu Fatimah',      'wa' => '081300000008', 'santri' => 'Ali bin Fatimah',            'nis' => '2627.02.0022', 'reg' => '2026.0027', 'unit' => '02', 'res' => 'NON_MONDOK', 'special' => 'ANAK_GURU', 'level' => $level10, 'rombel' => $rombel10A],
            ['wali' => 'Bapak Taufiq',     'wa' => '081300000009', 'santri' => 'Taufiq Jr bin Taufiq',      'nis' => '2627.01.0023', 'reg' => '2026.0028', 'unit' => '01', 'res' => 'MONDOK',     'special' => 'UMUM',  'level' => $level8,       'rombel' => $rombel8B],
            ['wali' => 'Ibu Maryam',       'wa' => '081300000010', 'santri' => 'Isa bin Maryam',             'nis' => '2627.03.0022', 'reg' => '2026.0029', 'unit' => '03', 'res' => 'MONDOK',     'special' => 'YATIM', 'level' => $levelTahfidz, 'rombel' => $rombelTahfidzA],
            ['wali' => 'Bapak Harun',      'wa' => '081300000011', 'santri' => 'Musa bin Harun',             'nis' => '2627.01.0024', 'reg' => '2026.0030', 'unit' => '01', 'res' => 'MONDOK',     'special' => 'UMUM',  'level' => $level7,       'rombel' => $rombel7A],
            ['wali' => 'Ibu Safiyyah',     'wa' => '081300000012', 'santri' => 'Khalid bin Safiyyah',        'nis' => '2627.02.0023', 'reg' => '2026.0031', 'unit' => '02', 'res' => 'MONDOK',     'special' => 'UMUM',  'level' => $level10,      'rombel' => $rombel10A],
        ];

        $createdStudents = [];

        foreach ($newStudents as $data) {
            $user = User::create([
                'name'     => $data['wali'],
                'whatsapp' => $data['wa'],
                'email'    => null,
                'password' => Hash::make('password'),
                'role'     => 'WALI_SANTRI',
            ]);

            $guardian = Guardian::create([
                'user_id'   => $user->id,
                'full_name' => $data['wali'],
                'whatsapp'  => $data['wa'],
            ]);

            $student = Student::create([
                'guardian_id'       => $guardian->id,
                'spmb_schedule_id'  => $spmbSchedule?->id,
                'nis'               => $data['nis'],
                'registration_number' => $data['reg'],
                'full_name'         => $data['santri'],
                'unit_code'         => $data['unit'],
                'residence_status'  => $data['res'],
                'special_status'    => $data['special'],
                'status'            => StudentStatus::ACCEPTED->value,
                'class_level_id'    => $data['level']?->id,
                'study_group_id'    => $data['rombel']?->id,
                'address'           => 'Kab. Bogor',
                'is_active'         => true,
            ]);

            $createdStudents[] = [
                'student' => $student,
                'unit'    => $data['unit'],
                'special' => $data['special'],
            ];
        }

        // ──────────────────────────────────────────────
        // Kumpulkan semua 20 santri (8 lama + 12 baru)
        // ──────────────────────────────────────────────
        $existingStudents = [
            ['name' => 'Ahmad Santri',          'unit' => '01', 'special' => 'UMUM'],
            ['name' => 'Muhammad Ridwan Jr',     'unit' => '02', 'special' => 'ANAK_GURU'],
            ['name' => 'Aisyah binti Ridwan',    'unit' => '03', 'special' => 'UMUM'],
            ['name' => 'Zahra binti Rahmah',     'unit' => '01', 'special' => 'YATIM'],
            ['name' => 'Fathir Syarifuddin',     'unit' => '01', 'special' => 'UMUM'],
            ['name' => 'Fania Syarifuddin',      'unit' => '02', 'special' => 'UMUM'],
            ['name' => 'Farhan Syarifuddin',     'unit' => '03', 'special' => 'UMUM'],
            ['name' => 'Farras Syarifuddin',     'unit' => '01', 'special' => 'UMUM'],
        ];

        $allStudents = [];

        foreach ($existingStudents as $data) {
            $student = Student::where('full_name', $data['name'])->first();
            if ($student) {
                $allStudents[] = [
                    'student' => $student,
                    'unit'    => $data['unit'],
                    'special' => $data['special'],
                ];
            }
        }

        $allStudents = array_merge($allStudents, $createdStudents);

        // ──────────────────────────────────────────────
        // Buat tagihan terlambat: 1-2 per santri, total ~30
        // ──────────────────────────────────────────────
        $sppMap = [
            '01' => $fmSPP_SMP,
            '02' => $fmSPP_SMA,
            '03' => $fmSPP_PPTQ,
        ];

        $discountMap = [
            'ANAK_GURU' => 3000,
            'YATIM'     => 2000,
        ];

        $billingCount = 0;

        foreach ($allStudents as $i => $data) {
            $student = $data['student'];
            $spp = $sppMap[$data['unit']] ?? $fmSPP_SMP;
            $discount = $discountMap[$data['special']] ?? 0;

            // Setiap santri dapat 1 tagihan SPP April (overdue)
            Billing::create([
                'student_id'       => $student->id,
                'fee_master_id'    => $spp->id,
                'title'            => "SPP {$this->unitLabel($data['unit'])} April 2026",
                'original_amount'  => $spp->amount,
                'discount_applied' => $discount,
                'final_amount'     => $spp->amount - $discount,
                'status'           => 'UNPAID',
                'due_date'         => Carbon::parse('2026-04-15'),
                'created_at'       => Carbon::parse('2026-04-01'),
            ]);
            $billingCount++;

            // 10 santri pertama juga dapat tagihan Mei (supaya total ~30)
            if ($i < 10) {
                Billing::create([
                    'student_id'       => $student->id,
                    'fee_master_id'    => $spp->id,
                    'title'            => "SPP {$this->unitLabel($data['unit'])} Mei 2026",
                    'original_amount'  => $spp->amount,
                    'discount_applied' => $discount,
                    'final_amount'     => $spp->amount - $discount,
                    'status'           => 'UNPAID',
                    'due_date'         => Carbon::parse('2026-05-15'),
                    'created_at'       => Carbon::parse('2026-05-01'),
                ]);
                $billingCount++;
            }
        }

        $this->command->info("OverdueTestSeeder: Created {$billingCount} overdue billings for " . count($allStudents) . " students.");

        }); // end Billing::withoutEvents
    }

    private function unitLabel(string $unit): string
    {
        return match ($unit) {
            '01' => 'SMP',
            '02' => 'SMA',
            '03' => 'PPTQ',
            default => $unit,
        };
    }
}
