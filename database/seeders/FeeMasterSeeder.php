<?php

namespace Database\Seeders;

use App\Models\FeeMaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeeMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // SMP Fees
        FeeMaster::create([
            'unit_target' => '01', // SMP
            'residence_target' => null,
            'category' => 'BULANAN',
            'item_name' => 'SPP SMP',
            'amount' => 150000,
        ]);

        // SMA Fees
        FeeMaster::create([
            'unit_target' => '02', // SMA
            'residence_target' => null,
            'category' => 'BULANAN',
            'item_name' => 'SPP SMA',
            'amount' => 200000,
        ]);

        // PPTQ (Pondok) Fees
        FeeMaster::create([
            'unit_target' => null, // All units if boarding
            'residence_target' => 'MONDOK',
            'category' => 'BULANAN',
            'item_name' => 'Biaya Makan & Asrama',
            'amount' => 450000,
        ]);

        // Daftar Ulang (Yearly)
        FeeMaster::create([
            'unit_target' => null,
            'residence_target' => null,
            'category' => 'DAFTAR_ULANG',
            'item_name' => 'Daftar Ulang Tahunan',
            'amount' => 1000000,
        ]);
    }
}
