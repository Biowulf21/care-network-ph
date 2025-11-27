<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class MedicalRecordsSeeder extends Seeder
{
    public function run(): void
    {
        // attach some medical records to recent patients
        Patient::inRandomOrder()->limit(500)->get()->each(function (Patient $patient) {
            $clinicId = $patient->clinic_id;
            MedicalRecord::factory()->count(rand(1, 4))->create([
                'patient_id' => $patient->id,
                'clinic_id' => $clinicId,
                'user_id' => User::inRandomOrder()->first()?->id,
            ]);
        });
    }
}
