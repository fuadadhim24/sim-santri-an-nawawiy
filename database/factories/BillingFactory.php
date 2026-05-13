<?php

namespace Database\Factories;

use App\Models\Billing;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class BillingFactory extends Factory
{
    protected $model = Billing::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'title' => $this->faker->words(3, true),
            'original_amount' => $this->faker->numberBetween(100000, 5000000),
            'discount_applied' => $this->faker->numberBetween(0, 500000),
            'final_amount' => $this->faker->numberBetween(100000, 5000000),
            'status' => $this->faker->randomElement(['UNPAID', 'PAID']),
            'visible_to_wali' => true,
        ];
    }

    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'UNPAID',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'PAID',
        ]);
    }
}
