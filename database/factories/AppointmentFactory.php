<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Center;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        // Create a center, then bind doctor & patient to it to avoid mismatch
        $center = Center::factory();
        $doctor = Doctor::factory()->for($center);
        $patient = Patient::factory()->for($center);

        return [
            'patient_id' => $patient,
            'doctor_id' => $doctor,
            'center_id' => $center,
            'status' => fake()->randomElement(['pending', 'confirmed', 'cancelled']),
            'date' => fake()->dateTimeBetween('+1 day', '+1 month')->format('Y-m-d'),
            'time' => fake()->time('H:i:s'),
            'intended' => fake()->boolean(15),
        ];
    }
}
