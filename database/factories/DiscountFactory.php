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
            'target_status' => $this->faker->randomElement(['ANAK_GURU', 'YATIM', 'PRESTASI']),
            'discount_amount' => $this->faker->numberBetween(5000, 50000),
        ];
    }
}
