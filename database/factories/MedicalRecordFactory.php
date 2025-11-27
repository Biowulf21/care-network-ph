<?php

namespace Database\Factories;

use App\Models\MedicalRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;

    public function definition(): array
    {
        return [
            'patient_id' => null, // set in seeder
            'clinic_id' => null, // set in seeder
            'user_id' => null,
            'consultation_date' => $this->faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d'),
            'vitals' => [
                'bp' => $this->faker->randomElement(['120/80', '130/85', '110/70']),
                'hr' => $this->faker->numberBetween(60, 110),
                'temp' => $this->faker->randomFloat(1, 36, 39),
                'rr' => $this->faker->numberBetween(12, 24),
            ],
            'doctor_notes' => $this->faker->paragraph,
            'diagnosis' => $this->faker->sentence,
            'treatment_plan' => $this->faker->paragraph,
            'medical_history' => [
                'past_conditions' => $this->faker->words(3),
                'medications' => $this->faker->words(2),
                'allergies' => $this->faker->optional()->words(2),
                'family_history' => $this->faker->optional()->sentence,
            ],
            'philhealth' => [
                'case_rate_code' => $this->faker->bothify('CR-###'),
                'claim_status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            ],
            'documents_checklist' => [
                'consent_signed' => $this->faker->boolean(80),
                'id_copy' => $this->faker->boolean(90),
            ],
            'admission' => null,
            'discharge' => null,
        ];
    }
}
