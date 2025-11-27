<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $doctors = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'delegate']))->get();
        $clinics = Clinic::all();

        $appointmentTypes = ['Consultation', 'Follow-up', 'Vaccination', 'Physical Exam', 'Lab Review'];
        $specialties = ['General Medicine', 'Pediatrics', 'Internal Medicine', 'Family Medicine'];
        $statuses = ['scheduled', 'confirmed', 'completed', 'cancelled'];

        // Create past appointments
        for ($i = 0; $i < 150; $i++) {
            $patient = $patients->random();
            $doctor = $doctors->where('clinic_id', $patient->clinic_id)->first() ?? $doctors->random();

            Appointment::create([
                'patient_id' => $patient->id,
                'clinic_id' => $patient->clinic_id,
                'user_id' => $doctor->id,
                'appointment_date' => Carbon::now()->subDays(rand(1, 90)),
                'appointment_time' => sprintf('%02d:%02d:00', rand(8, 17), rand(0, 59)),
                'appointment_type' => fake()->randomElement($appointmentTypes),
                'status' => 'completed',
                'notes' => rand(0, 1) ? fake()->sentence() : null,
                'service_type' => fake()->randomElement(['Consultation', 'Procedure', 'Therapy']),
                'specialty' => fake()->randomElement($specialties),
                'is_urgent' => rand(0, 10) > 8, // 20% urgent
            ]);
        }

        // Create upcoming appointments
        for ($i = 0; $i < 50; $i++) {
            $patient = $patients->random();
            $doctor = $doctors->where('clinic_id', $patient->clinic_id)->first() ?? $doctors->random();

            Appointment::create([
                'patient_id' => $patient->id,
                'clinic_id' => $patient->clinic_id,
                'user_id' => $doctor->id,
                'appointment_date' => Carbon::now()->addDays(rand(1, 60)),
                'appointment_time' => sprintf('%02d:%02d:00', rand(8, 17), rand(0, 59)),
                'appointment_type' => fake()->randomElement($appointmentTypes),
                'status' => fake()->randomElement(['scheduled', 'confirmed']),
                'notes' => rand(0, 1) ? fake()->sentence() : null,
                'service_type' => fake()->randomElement(['Consultation', 'Procedure', 'Therapy']),
                'specialty' => fake()->randomElement($specialties),
                'is_urgent' => rand(0, 10) > 8,
            ]);
        }
    }
}
