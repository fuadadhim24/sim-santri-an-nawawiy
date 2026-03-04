<?php

namespace Database\Seeders;

use App\Enums\ActivationMode;
use App\Models\FeeCategory;
use Illuminate\Database\Seeder;

class FeeCategorySeeder extends Seeder
{
    public function run(): void
    {
        FeeCategory::updateOrCreate(
            ['code' => 'SPMB'],
            [
                'name' => 'Biaya Pendaftaran',
                'code' => 'SPMB',
                'description' => 'Biaya pendaftaran SPMB',
                'is_locked' => true,
                'activation_mode' => ActivationMode::MANUAL_ONLY->value,
                'can_generate_before_acceptance' => false,
            ]
        );

        FeeCategory::updateOrCreate(
            ['code' => 'SPP'],
            [
                'name' => 'Sumbangan Pembinaan Pendidikan',
                'code' => 'SPP',
                'description' => 'Biaya SPP bulanan',
                'is_locked' => false,
                'activation_mode' => ActivationMode::SINGLE_ACTIVE_PER_KEY->value,
                'can_generate_before_acceptance' => true,
            ]
        );

        FeeCategory::updateOrCreate(
            ['code' => 'REG'],
            [
                'name' => 'Pendaftaran',
                'code' => 'REG',
                'description' => 'Biaya pendaftaran awal tahun ajaran',
                'is_locked' => false,
                'activation_mode' => ActivationMode::MULTI_ACTIVE->value,
                'can_generate_before_acceptance' => true,
            ]
        );

        FeeCategory::updateOrCreate(
            ['code' => 'RE_REG'],
            [
                'name' => 'Daftar Ulang',
                'code' => 'RE_REG',
                'description' => 'Biaya daftar ulang semester',
                'is_locked' => false,
                'activation_mode' => ActivationMode::MULTI_ACTIVE->value,
                'can_generate_before_acceptance' => true,
            ]
        );

        FeeCategory::updateOrCreate(
            ['code' => 'POCKET'],
            [
                'name' => 'Uang Saku',
                'code' => 'POCKET',
                'description' => 'Uang saku bulanan untuk santri mondok',
                'is_locked' => false,
                'activation_mode' => ActivationMode::MULTI_ACTIVE->value,
                'can_generate_before_acceptance' => true,
                'domicile_target' => 'MONDOK', // Hanya untuk santri mondok
            ]
        );

        FeeCategory::updateOrCreate(
            ['code' => 'ASRAMA'],
            [
                'name' => 'Asrama',
                'code' => 'ASRAMA',
                'description' => 'Biaya asrama untuk santri mondok',
                'is_locked' => false,
                'activation_mode' => ActivationMode::MULTI_ACTIVE->value,
                'can_generate_before_acceptance' => true,
                'domicile_target' => 'MONDOK', // Hanya untuk santri mondok
            ]
        );

        FeeCategory::updateOrCreate(
            ['code' => 'OTHER'],
            [
                'name' => 'Lain-lain',
                'code' => 'OTHER',
                'description' => 'Biaya lainnya sesuai kebutuhan',
                'is_locked' => false,
                'activation_mode' => ActivationMode::MULTI_ACTIVE->value,
                'can_generate_before_acceptance' => true,
            ]
        );
    }
}
