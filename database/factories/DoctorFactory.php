<?php

namespace Database\Factories;

use App\Models\Center;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'center_id' => Center::factory(),
            'user_id' => User::factory(),
            'specialization' => fake()->randomElement([
                'باطنة', 'أشعة', 'أنف وأذن وحنجرة'
            ]),
            'available' => fake()->boolean(90),
        ];
    }
}
