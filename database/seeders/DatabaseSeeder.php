<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan penting karena foreign key dependencies:
     * 1. SpmbSchedule — tidak ada dependency
     * 2. User/Guardian/Student — student FK ke spmb_schedule
     * 3. FeeCategory — tidak ada dependency
     * 4. FeeMaster — FK ke fee_category
     * 5. Discount — FK ke fee_master
     * 6. Billing/Payment — FK ke student, fee_master, user(admin)
     */
    public function run(): void
    {
        // Copy seeder files to storage
        try {
            $storage = \Illuminate\Support\Facades\Storage::disk('public');
            
            $filesToCopy = [
                'kk' => ['source' => 'pondok.png', 'target' => 'students/kk/seeder_kk.png'],
                'foto' => ['source' => 'user.png', 'target' => 'students/foto/seeder_foto.png'],
                'akta' => ['source' => 'smpq.png', 'target' => 'students/akta/seeder_akta.png'],
                'nisn' => ['source' => 'pondok.png', 'target' => 'students/nisn/seeder_nisn.png'],
                'ijazah' => ['source' => 'smpq.png', 'target' => 'students/ijazah/seeder_ijazah.png'],
            ];

            foreach ($filesToCopy as $key => $info) {
                $sourcePath = public_path('image/' . $info['source']);
                if (file_exists($sourcePath) && !$storage->exists($info['target'])) {
                    $storage->put($info['target'], file_get_contents($sourcePath));
                }
            }
        } catch (\Exception $e) {
            // Safe fallback
        }

        // Register global listener to auto-populate files for all students created during seeding
        \App\Models\Student::creating(function (\App\Models\Student $student) {
            if (empty($student->kk)) {
                $student->kk = 'students/kk/seeder_kk.png';
            }
            if (empty($student->foto)) {
                $student->foto = 'students/foto/seeder_foto.png';
            }
            if (empty($student->akta)) {
                $student->akta = 'students/akta/seeder_akta.png';
            }
            if (empty($student->nisn_document) && rand(1, 100) <= 70) {
                $student->nisn_document = 'students/nisn/seeder_nisn.png';
            }
            if (empty($student->ijazah) && rand(1, 100) <= 50) {
                $student->ijazah = 'students/ijazah/seeder_ijazah.png';
            }
        });

        $this->call([
            SpmbScheduleSeeder::class,
            RombelSeeder::class,
            UserSeeder::class,
            FeeCategorySeeder::class,
            FeeMasterSeeder::class,
            DiscountSeeder::class,
            BillingSeeder::class,
            OverdueTestSeeder::class,
            FaqSeeder::class,
        ]);
    }
}
