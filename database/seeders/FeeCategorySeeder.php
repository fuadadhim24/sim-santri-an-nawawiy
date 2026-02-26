<?php

namespace Database\Seeders;

use App\Models\FeeCategory;
use Illuminate\Database\Seeder;

class FeeCategorySeeder extends Seeder
{
    public function run(): void
    {
        FeeCategory::create(['name' => 'SPP', 'code' => 'SPP', 'billing_interval' => 'MONTHLY']);
        FeeCategory::create(['name' => 'Pendaftaran', 'code' => 'REG', 'billing_interval' => 'ONCE']);
        FeeCategory::create(['name' => 'Daftar Ulang', 'code' => 'RE_REG', 'billing_interval' => 'YEARLY']);
        FeeCategory::create(['name' => 'Uang Saku', 'code' => 'POCKET', 'billing_interval' => 'MONTHLY']);
        FeeCategory::create(['name' => 'Lain-lain', 'code' => 'OTHER', 'billing_interval' => 'ONCE']);
    }
}
