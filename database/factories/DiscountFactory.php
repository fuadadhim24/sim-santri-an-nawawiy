<?php

namespace Database\Factories;

use App\Models\Discount;
use App\Models\FeeMaster;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    public function definition(): array
    {
        return [
            'fee_master_id' => FeeMaster::factory(),
            'target_status' => fn () => \App\Models\SpecialStatus::where('code', '!=', 'UMUM')->inRandomOrder()->first()?->code ?? 'ANAK_GURU',
            'discount_amount' => $this->faker->numberBetween(5000, 50000),
        ];
    }
}
