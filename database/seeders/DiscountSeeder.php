<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\FeeMaster;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Seed discounts untuk fee masters.
     *
     * Discount target_status enum: ANAK_GURU, YATIM
     * Setiap fee master bulanan (SPP, Asrama, Uang Saku) mendapat
     * potongan untuk santri YATIM dan ANAK_GURU.
     */
    public function run(): void
    {
        $fmSPP_SMP  = FeeMaster::where('item_name', 'SPP SMP')->first();
        $fmSPP_SMA  = FeeMaster::where('item_name', 'SPP SMA')->first();
        $fmSPP_PPTQ = FeeMaster::where('item_name', 'SPP PPTQ')->first();
        $fmAsrama   = FeeMaster::where('item_name', 'Biaya Asrama Bulanan')->first();
        $fmPocket   = FeeMaster::where('item_name', 'Uang Saku Bulanan')->first();

        // ──────────────────────────────────────────────
        // Diskon YATIM — potongan Rp 2.000 per fee
        // ──────────────────────────────────────────────
        $yatimFees = [$fmSPP_SMP, $fmSPP_SMA, $fmSPP_PPTQ, $fmAsrama, $fmPocket];
        foreach ($yatimFees as $fm) {
            if ($fm) {
                Discount::create([
                    'fee_master_id' => $fm->id,
                    'target_status' => 'YATIM',
                    'discount_amount' => 2000,
                ]);
            }
        }

        // ──────────────────────────────────────────────
        // Diskon ANAK_GURU — potongan Rp 3.000 per fee
        // ──────────────────────────────────────────────
        $anakGuruFees = [$fmSPP_SMP, $fmSPP_SMA, $fmSPP_PPTQ, $fmAsrama, $fmPocket];
        foreach ($anakGuruFees as $fm) {
            if ($fm) {
                Discount::create([
                    'fee_master_id' => $fm->id,
                    'target_status' => 'ANAK_GURU',
                    'discount_amount' => 3000,
                ]);
            }
        }
    }
}
