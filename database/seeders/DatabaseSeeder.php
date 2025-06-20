<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Center;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vital;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'عبدالرحمن أبوليفة',
            'email' => 'admin@gmail.com',
        ]);
        Center::factory(5)->create()->each(function ($center) {
            User::factory(10)->create([
                'center_id' => $center->id,
            ]);
            Doctor::factory(fake()->numberBetween(3, 5))->create([
                'center_id' => $center->id,
            ]);
            Patient::factory(fake()->numberBetween(10, 15))->create([
                'center_id' => $center->id,
            ])->each(function ($patient) use ($center) {
                Vital::factory(fake()->numberBetween(3, 7))->create([
                    'patient_id' => $patient->id,
                ]);
            });
            foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day) {
                Schedule::factory()->create([
                    'center_id' => $center->id,
                    'day' => $day,
                ]);
            }
            $doctors = Doctor::where('center_id', $center->id)->get();
            $patients = Patient::where('center_id', $center->id)->get();
            for ($i = 0; $i < 20; $i++) {
                Appointment::factory()->create([
                    'center_id' => $center->id,
                    'doctor_id' => $doctors->random()->id,
                    'patient_id' => $patients->random()->id,
                ]);
            }
        });
        Product::factory(50)->create();
    }
}
