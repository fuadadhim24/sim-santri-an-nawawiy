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
            // new students start inactive and pending acceptance
            'is_active' => false,
            'status' => 'menunggu',
        ]);
    }
}
