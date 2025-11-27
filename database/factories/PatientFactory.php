<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        $dob = $this->faker->dateTimeBetween('-80 years', '-1 years');
        return [
            'clinic_id' => null, // set in seeder
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->optional()->firstName,
            'last_name' => $this->faker->lastName,
            'date_of_birth' => $dob->format('Y-m-d'),
            'sex' => $this->faker->randomElement(['male', 'female', 'other']),
            'philhealth_number' => $this->faker->bothify('PH-########'),
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'emergency_contact_name' => $this->faker->name,
            'emergency_contact_phone' => $this->faker->phoneNumber,
        ];
    }
}
