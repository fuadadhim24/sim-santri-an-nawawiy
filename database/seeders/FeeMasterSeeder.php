<?php

namespace Database\Seeders;

use App\Models\FeeCategory;
use App\Models\FeeMaster;
use Illuminate\Database\Seeder;

class FeeMasterSeeder extends Seeder
{
    public function run(): void
    {
        $catSPMB   = FeeCategory::where('code', 'SPMB')->first();
        $catSPP    = FeeCategory::where('code', 'SPP')->first();
        $catReg    = FeeCategory::where('code', 'REG')->first();
        $catReReg  = FeeCategory::where('code', 'RE_REG')->first();
        $catPocket = FeeCategory::where('code', 'POCKET')->first();
        $catAsrama = FeeCategory::where('code', 'ASRAMA')->first();
        $catOther  = FeeCategory::where('code', 'OTHER')->first();

        // ──────────────────────────────────────────────
        // Biaya Pendaftaran (REG) — semua unit, sekali bayar
        // ──────────────────────────────────────────────
        FeeMaster::create([
            'fee_category_id' => $catReg->id,
            'item_name' => 'Biaya Pendaftaran SPMB',
            'amount' => 10000,
            'start_date' => '2025-01-01',
            'unit_target' => null,
            'residence_target' => null,
        ]);

        // ──────────────────────────────────────────────
        // Biaya Daftar Ulang (RE_REG) — semua unit
        // ──────────────────────────────────────────────
        FeeMaster::create([
            'fee_category_id' => $catReReg->id,
            'item_name' => 'Biaya Daftar Ulang Semester',
            'amount' => 12000,
            'start_date' => '2025-01-01',
            'unit_target' => null,
            'residence_target' => null,
        ]);

        // ──────────────────────────────────────────────
        // SPP Bulanan — per unit
        // ──────────────────────────────────────────────
        FeeMaster::create([
            'fee_category_id' => $catSPP->id,
            'item_name' => 'SPP SMP',
            'amount' => 12000,
            'billing_day' => 10,
            'start_date' => '2025-01-01',
            'unit_target' => '01',
            'residence_target' => null,
        ]);

        FeeMaster::create([
            'fee_category_id' => $catSPP->id,
            'item_name' => 'SPP SMA',
            'amount' => 13000,
            'billing_day' => 10,
            'start_date' => '2025-01-01',
            'unit_target' => '02',
            'residence_target' => null,
        ]);

        FeeMaster::create([
            'fee_category_id' => $catSPP->id,
            'item_name' => 'SPP PPTQ',
            'amount' => 11000,
            'billing_day' => 10,
            'start_date' => '2025-01-01',
            'unit_target' => '03',
            'residence_target' => null,
        ]);

        // ──────────────────────────────────────────────
        // Uang Saku (POCKET) — hanya MONDOK
        // ──────────────────────────────────────────────
        FeeMaster::create([
            'fee_category_id' => $catPocket->id,
            'item_name' => 'Uang Saku Bulanan',
            'amount' => 10000,
            'billing_day' => 5,
            'start_date' => '2025-01-01',
            'unit_target' => null,
            'residence_target' => 'MONDOK',
        ]);

        // ──────────────────────────────────────────────
        // Asrama — hanya MONDOK
        // ──────────────────────────────────────────────
        FeeMaster::create([
            'fee_category_id' => $catAsrama->id,
            'item_name' => 'Biaya Asrama Bulanan',
            'amount' => 14000,
            'billing_day' => 10,
            'start_date' => '2025-01-01',
            'unit_target' => null,
            'residence_target' => 'MONDOK',
        ]);

        // ──────────────────────────────────────────────
        // Lain-lain (OTHER)
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
