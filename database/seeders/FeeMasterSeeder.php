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

        // ──────────────────────────────────────────────
        // Biaya Pendaftaran (Registrasi)
        // ──────────────────────────────────────────────
        FeeMaster::create([
            'fee_category_id' => $catReg->id,
            'item_name' => 'Biaya Pendaftaran SPMB',
            'amount' => 12000,
            'start_date' => '2025-01-01',
            'unit_target' => null,
            'residence_target' => null,
        ]);

        // ──────────────────────────────────────────────
        // Biaya Daftar Ulang (Re-Registrasi)
        // ──────────────────────────────────────────────
        FeeMaster::create([
            'fee_category_id' => $catReReg->id,
            'item_name' => 'Biaya Daftar Ulang Semester',
            'amount' => 15000,
            'start_date' => '2025-01-01',
            'unit_target' => null,
            'residence_target' => null,
        ]);

        // ──────────────────────────────────────────────
        // SPP per Jenjang
        // ──────────────────────────────────────────────
        FeeMaster::create([
            'fee_category_id' => $catSPP->id,
            'item_name' => 'SPP SMP',
            'amount' => 15000,
            'billing_day' => 10,
            'start_date' => '2025-01-01',
            'unit_target' => '01',
            'residence_target' => null,
        ]);

        FeeMaster::create([
            'fee_category_id' => $catSPP->id,
            'item_name' => 'SPP SMA',
            'amount' => 16000,
            'billing_day' => 10,
            'start_date' => '2025-01-01',
            'unit_target' => '02',
            'residence_target' => null,
        ]);

        FeeMaster::create([
            'fee_category_id' => $catSPP->id,
            'item_name' => 'SPP PPTQ',
            'amount' => 15500,
            'billing_day' => 10,
            'start_date' => '2025-01-01',
            'unit_target' => '03',
            'residence_target' => null,
        ]);

        // ──────────────────────────────────────────────
        // Biaya Bulanan Lainnya
        // ──────────────────────────────────────────────
        FeeMaster::create([
            'fee_category_id' => $catOther->id,
            'item_name' => 'Biaya Asrama Bulanan',
            'amount' => 14000,
            'billing_day' => 10,
            'start_date' => '2025-01-01',
            'unit_target' => null,
            'residence_target' => 'MONDOK',
        ]);

        FeeMaster::create([
            'fee_category_id' => $catOther->id,
            'item_name' => 'Uang Saku Bulanan',
            'amount' => 14500,
            'billing_day' => 10,
            'start_date' => '2025-01-01',
            'unit_target' => null,
            'residence_target' => 'MONDOK',
        ]);

        // ──────────────────────────────────────────────
        // Biaya Semester & Akhir Sekolah
        // ──────────────────────────────────────────────
        FeeMaster::create([
            'fee_category_id' => $catOther->id,
            'item_name' => 'Biaya Semester',
            'amount' => 15000,
            'start_date' => '2025-01-01',
            'unit_target' => null,
            'residence_target' => null,
        ]);

        FeeMaster::create([
            'fee_category_id' => $catOther->id,
            'item_name' => 'Biaya Akhir Sekolah',
            'amount' => 15000,
            'start_date' => '2025-01-01',
            'unit_target' => '02',
            'residence_target' => null,
        ]);
    }
}
