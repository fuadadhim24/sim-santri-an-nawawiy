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
                'is_locked' => false,
                'activation_mode' => ActivationMode::MULTI_ACTIVE->value,
                'can_generate_before_acceptance' => true,
            ]
        );

        FeeCategory::updateOrCreate(
            ['code' => 'OTHER'],
            [
                'name' => 'Lain-lain',
                'code' => 'OTHER',
                'is_locked' => false,
                'activation_mode' => ActivationMode::MULTI_ACTIVE->value,
                'can_generate_before_acceptance' => true,
            ]
        );
    }
}
