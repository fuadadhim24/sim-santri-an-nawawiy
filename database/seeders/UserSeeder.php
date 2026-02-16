<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@annawawiy.ac.id',
            'password' => Hash::make('password'),
            'role' => 'SUPER_ADMIN',
        ]);

        // 2. Admin TU
        User::create([
            'name' => 'Admin TU',
            'email' => 'tu@annawawiy.ac.id',
            'password' => Hash::make('password'),
            'role' => 'ADMIN_TU',
        ]);

        // 3. Wali Santri Sample
        $waliUser = User::create([
            'name' => 'H. Abdullah',
            'email' => 'wali@test.com',
            'password' => Hash::make('password'),
            'role' => 'WALI_SANTRI',
        ]);

        $guardian = Guardian::create([
            'user_id' => $waliUser->id,
            'full_name' => 'H. Abdullah',
            'whatsapp' => '081234567890',
        ]);

        // 4. Student Sample
        Student::create([
            'guardian_id' => $guardian->id,
            'nis' => '2026.02.0001',
            'full_name' => 'Ahmad Santri',
            'unit_code' => '02', // SMA
            'residence_status' => 'MONDOK',
            'special_status' => 'UMUM',
            'class_name' => '10 IPA',
            'address' => 'Jl. Pendidikan No. 1',
            'is_active' => true,
        ]);
    }
}
