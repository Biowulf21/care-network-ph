<?php

namespace Database\Factories;

use App\Models\MedicalRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;

    public function definition(): array
    {
        $faker = $this->faker;

        return [
            'patient_id' => null, // set in seeder
            'clinic_id' => null, // set in seeder
            'user_id' => null,
            'consultation_date' => $faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d'),
            'vitals' => [
                'bp' => $faker->randomElement(['120/80', '130/85', '110/70']),
                'hr' => $faker->numberBetween(60, 110),
                'temp' => $faker->randomFloat(1, 36, 39),
                'rr' => $faker->numberBetween(12, 24),
            ],
            'doctor_notes' => $faker->paragraph,
            'diagnosis' => $faker->sentence,
            'treatment_plan' => $faker->paragraph,
            'medical_history' => [
                'past_conditions' => $faker->words(3),
                'medications' => $faker->words(2),
                'allergies' => $faker->optional()->words(2),
                'family_history' => $faker->optional()->sentence,
            ],
            'philhealth' => [
                'case_rate_code' => $faker->bothify('CR-###'),
                'claim_status' => $faker->randomElement(['pending', 'approved', 'rejected']),
            ],
            'documents_checklist' => [
                'consent_signed' => $faker->boolean(80),
                'id_copy' => $faker->boolean(90),
            ],
            'admission' => null,
            'discharge' => null,
        ];
    }
}
