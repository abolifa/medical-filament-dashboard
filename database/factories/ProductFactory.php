<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hasExpiry = fake()->boolean();
        return [
            'type' => fake()->randomElement(['medicine', 'equipment', 'service']),
            'name' => fake()->unique()->word() . ' ' . fake()->randomElement(['Kit', 'Tablet', 'Solution', 'Service']),
            'image' => fake()->optional()->imageUrl(400, 400, 'medical', true),
            'expiry' => $hasExpiry,
            'expiry_date' => $hasExpiry
                ? fake()->dateTimeBetween('now', '+2 years')->format('Y-m-d')
                : null,
            'usage' => fake()->optional()->sentence(12),
            'dosage' => fake()->optional()->sentence(8),
        ];
    }
}
