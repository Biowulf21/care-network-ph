<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientsSeeder extends Seeder
{
    public function run(): void
    {
        Clinic::all()->each(function (Clinic $clinic) {
            Patient::factory()->count(30)->create(['clinic_id' => $clinic->id]);
        });
    }
}
