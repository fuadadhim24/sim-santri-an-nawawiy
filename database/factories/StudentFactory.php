<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'guardian_id' => Guardian::factory(),
            'nis' => $this->faker->unique()->regexify('[0-9]{10}'),
            'registration_number' => $this->faker->unique()->numerify('2026.####'),
            'nisn' => $this->faker->unique()->regexify('[0-9]{10}'),
            'full_name' => $this->faker->name(),
            'unit_code' => $this->faker->randomElement(['01', '02', '03']),
            'residence_status' => $this->faker->randomElement(['MONDOK', 'NON_MONDOK', 'NGAJI_ONLY']),
            'special_status' => $this->faker->randomElement(['UMUM', 'ANAK_GURU', 'YATIM']),
            'class_name' => 'Kelas ' . $this->faker->randomElement(['X', 'XI', 'XII']),
            'address' => $this->faker->address(),
            'is_active' => $this->faker->boolean(),
            'status' => 'ACTIVE',
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ACTIVE',
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'INACTIVE',
            'is_active' => false,
        ]);
    }
}
