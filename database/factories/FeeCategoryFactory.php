<?php

namespace Database\Factories;

use App\Models\FeeCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeeCategoryFactory extends Factory
{
    protected $model = FeeCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'code' => $this->faker->unique()->bothify('??###'),
            'is_locked' => false,
            'activation_mode' => 'multi_active',
            'can_generate_before_acceptance' => true,
        ];
    }
}
