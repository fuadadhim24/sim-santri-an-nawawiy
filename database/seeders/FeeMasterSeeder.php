<?php

namespace Database\Seeders;

use App\Models\FeeCategory;
use App\Models\FeeMaster;
use Illuminate\Database\Seeder;

class FeeMasterSeeder extends Seeder
{
    public function run(): void
    {
        $catSPP = FeeCategory::where('code', 'SPP')->first();
        $catReg = FeeCategory::where('code', 'REG')->first();
        $catReReg = FeeCategory::where('code', 'RE_REG')->first();
        $catOther = FeeCategory::where('code', 'OTHER')->first();

        FeeMaster::create([
            'fee_category_id' => $catReg->id,
            'item_name' => 'Biaya Pendaftaran SPMB',
            'amount' => 500,

            'start_date' => '2025-01-01',
            'unit_target' => null,
            'residence_target' => null,
        ]);

        FeeMaster::create([
            'fee_category_id' => $catReReg->id,
            'item_name' => 'Biaya Daftar Ulang SPMB',
            'amount' => 800,

            'start_date' => '2025-01-01',
            'unit_target' => null,
            'residence_target' => null,
        ]);

        FeeMaster::create([
            'fee_category_id' => $catSPP->id,
            'item_name' => 'SPP SMP',
            'amount' => 1000,

            'billing_day' => 10,
            'start_date' => '2025-01-01',
            'unit_target' => '01',
            'residence_target' => null,
        ]);

        FeeMaster::create([
            'fee_category_id' => $catSPP->id,
            'item_name' => 'SPP SMA',
            'amount' => 1200,

            'billing_day' => 10,
            'start_date' => '2025-01-01',
            'unit_target' => '02',
            'residence_target' => null,
        ]);

        FeeMaster::create([
            'fee_category_id' => $catOther->id,
            'item_name' => 'Biaya Makan & Asrama',
            'amount' => 1300,

            'billing_day' => 10,
            'start_date' => '2025-01-01',
            'unit_target' => null,
            'residence_target' => 'MONDOK',
        ]);

        FeeMaster::create([
            'fee_category_id' => $catOther->id, // Semesteran set to Other
            'item_name' => 'Biaya Semester',
            'amount' => 1400,

            'start_date' => '2025-01-01',
            'unit_target' => null,
            'residence_target' => null,
        ]);

        FeeMaster::create([
            'fee_category_id' => $catOther->id, // Akhir sekolah set to Other
            'item_name' => 'Biaya Akhir Sekolah',
            'amount' => 1500,

            'start_date' => '2025-01-01',
            'unit_target' => '02',
            'residence_target' => null,
        ]);
    }
}
