<?php

namespace Database\Factories;

use App\Models\FeeMaster;
use App\Models\FeeCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeeMasterFactory extends Factory
{
    protected $model = FeeMaster::class;

    public function definition(): array
    {
        return [
            'item_name' => $this->faker->words(2, true),
            'amount' => $this->faker->numberBetween(100000, 5000000),
            'fee_category_id' => FeeCategory::factory(),
            'unit_target' => $this->faker->randomElement([null, 'MTK', 'TSANAWIYAH', 'ALIYAH']),
            'residence_target' => $this->faker->randomElement([null, 'MUKIM', 'NON_MUKIM']),
            'start_date' => now(),
            'end_date' => now()->addMonths(3),
            'is_active' => true,
        ];
    }
}
