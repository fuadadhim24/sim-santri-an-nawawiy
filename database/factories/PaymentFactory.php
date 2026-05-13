<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Billing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'billing_id' => Billing::factory(),
            'admin_id' => User::factory(),
            'method' => $this->faker->randomElement(['cash', 'duitku']),
            'amount' => $this->faker->numberBetween(100000, 5000000),
            'status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
            'paid_at' => $this->faker->boolean() ? $this->faker->dateTime() : null,
        ];
    }

    public function cash(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'cash',
            'status' => 'paid',
            'paid_at' => $this->faker->dateTime(),
        ]);
    }

    public function duitku(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'duitku',
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}
