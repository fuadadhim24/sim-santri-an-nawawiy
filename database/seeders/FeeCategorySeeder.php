<?php

namespace Database\Seeders;

use App\Models\FeeCategory;
use Illuminate\Database\Seeder;

class FeeCategorySeeder extends Seeder
{
    public function run(): void
    {
        FeeCategory::create(['name' => 'SPP', 'code' => 'SPP']);
        FeeCategory::create(['name' => 'Pendaftaran', 'code' => 'REG']);
        FeeCategory::create(['name' => 'Daftar Ulang', 'code' => 'RE_REG']);
        FeeCategory::create(['name' => 'Uang Saku', 'code' => 'POCKET']);
        FeeCategory::create(['name' => 'Lain-lain', 'code' => 'OTHER']);
    }
}
