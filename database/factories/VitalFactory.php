<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Vital;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vital>
 */
class VitalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'recorded_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'systolic' => fake()->numberBetween(90, 160),
            'diastolic' => fake()->numberBetween(60, 100),
            'pulse' => fake()->numberBetween(60, 120),
            'temperature' => fake()->randomFloat(1, 36.0, 39.0),
            'oxygen' => fake()->numberBetween(70, 100),
            'weight' => fake()->randomFloat(2, 40, 120),
        ];
    }
}
