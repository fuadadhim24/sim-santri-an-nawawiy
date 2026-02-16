<?php

namespace Database\Seeders;

use App\Models\FeeMaster;
use Illuminate\Database\Seeder;

class FeeMasterSeeder extends Seeder
{
    public function run(): void
    {
        FeeMaster::create([
            'unit_target' => null,
            'residence_target' => null,
            'category' => 'PENDAFTARAN',
            'item_name' => 'Biaya Pendaftaran SPMB',
            'amount' => 150000,
        ]);

        FeeMaster::create([
            'unit_target' => null,
            'residence_target' => null,
            'category' => 'DAFTAR_ULANG',
            'item_name' => 'Biaya Daftar Ulang SPMB',
            'amount' => 1250000,
        ]);

        FeeMaster::create([
            'unit_target' => '01',
            'residence_target' => null,
            'category' => 'BULANAN',
            'item_name' => 'SPP SMP',
            'amount' => 150000,
        ]);

        FeeMaster::create([
            'unit_target' => '02',
            'residence_target' => null,
            'category' => 'BULANAN',
            'item_name' => 'SPP SMA',
            'amount' => 200000,
        ]);

        FeeMaster::create([
            'unit_target' => null,
            'residence_target' => 'MONDOK',
            'category' => 'BULANAN',
            'item_name' => 'Biaya Makan & Asrama',
            'amount' => 450000,
        ]);

        FeeMaster::create([
            'unit_target' => null,
            'residence_target' => null,
            'category' => 'SEMESTERAN',
            'item_name' => 'Biaya Semester',
            'amount' => 300000,
        ]);

        FeeMaster::create([
            'unit_target' => '02',
            'residence_target' => null,
            'category' => 'AKHIR_SEKOLAH',
            'item_name' => 'Biaya Akhir Sekolah',
            'amount' => 1500000,
        ]);
    }
}
