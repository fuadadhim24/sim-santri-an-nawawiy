<?php

namespace Database\Factories;

use App\Models\SpmbSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpmbScheduleFactory extends Factory
{
    protected $model = SpmbSchedule::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'start_date' => $this->faker->dateThisYear(),
            'end_date' => $this->faker->dateThisYear(),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
