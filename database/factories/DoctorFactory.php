<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition()
    {
        $faker = $this->faker;

        $clinic = Clinic::inRandomOrder()->first() ?: Clinic::factory()->create();

        return [
            'clinic_id' => $clinic->id,
            'name' => $faker->name(),
            'specialty' => $faker->randomElement(['Family Medicine','Internal Medicine','Pediatrics','OB-GYN','Surgery','General Practitioner']),
            'phone' => $faker->phoneNumber(),
            'email' => $faker->unique()->safeEmail(),
        ];
    }
}
