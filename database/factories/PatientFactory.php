<?php

namespace Database\Factories;

use App\Models\Center;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'center_id' => fake()->boolean(80) ? Center::factory() : null,
            'name' => fake()->name(),
            'national_id' => fake()->unique()->numerify('############'),
            'family_issue_number' => fake()->optional()->regexify('[1-9][0-9]{5}'),
            'medical_file_number' => fake()->optional()->regexify('[A-Z]{2}[0-9]{6}'),
            'phone' => fake()->unique()->numerify('09########'),
            'password' => bcrypt('091091'),
            'remember_token' => Str::random(10),
            'active' => fake()->boolean(90),
        ];
    }
}
