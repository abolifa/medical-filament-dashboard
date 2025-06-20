<?php

namespace Database\Factories;

use App\Models\Center;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Center>
 */
class CenterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'مركز ' . fake()->unique()->city(),
            'primary_phone' => fake()->unique()->numerify('091#######'),
            'secondary_phone' => fake()->unique()->numerify('091#######'),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->streetAddress(),
            'street' => fake()->streetName(),
            'city' => fake()->city(),
            'latitude' => fake()->latitude(26, 32), // roughly Libya bounds
            'longitude' => fake()->longitude(9, 25),
            'location' => null,
        ];
    }
}
