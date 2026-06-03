<?php

namespace Database\Factories;

use App\Models\SpmbSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpmbScheduleFactory extends Factory
{
    protected $model = SpmbSchedule::class;

    public function definition(): array
    {
        $registrationStart = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $registrationEnd = $this->faker->dateTimeBetween($registrationStart, '+2 months');

        return [
            'name' => $this->faker->words(3, true),
            'registration_start' => $registrationStart,
            'registration_end' => $registrationEnd,
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
