<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@annawawiy.ac.id',
            'password' => Hash::make('password'),
            'role' => 'SUPER_ADMIN',
        ]);

        User::create([
            'name' => 'Admin TU',
            'email' => 'tu@annawawiy.ac.id',
            'password' => Hash::make('password'),
            'role' => 'ADMIN_TU',
        ]);

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

        Student::create([
            'guardian_id' => $guardian->id,
            'nis' => '2026.01.0001',
            'full_name' => 'Ahmad Santri',
            'unit_code' => '01',
            'residence_status' => 'MONDOK',
            'special_status' => 'UMUM',
            'class_name' => '7A',
            'address' => 'Jl. Pendidikan No. 1',
            'is_active' => false,
            'status' => 'menunggu',
        ]);

        // Add second guardian for IDOR testing
        $waliUser2 = User::create([
            'name' => 'Ibu Siti',
            'email' => 'siti@test.com',
            'password' => Hash::make('password'),
            'role' => 'WALI_SANTRI',
        ]);

        $guardian2 = Guardian::create([
            'user_id' => $waliUser2->id,
            'full_name' => 'Ibu Siti Mariyam',
            'whatsapp' => '082345678901',
        ]);

        Student::create([
            'guardian_id' => $guardian2->id,
            'nis' => '2026.01.0002',
            'full_name' => 'Fatima Binti Usman',
            'unit_code' => '01',
            'residence_status' => 'MONDOK',
            'special_status' => 'UMUM',
            'class_name' => '7B',
            'address' => 'Jl. Pesantren No. 5',
            'is_active' => false,
            'status' => 'menunggu',
        ]);
    }
}
