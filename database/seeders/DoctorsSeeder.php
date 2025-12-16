<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorsSeeder extends Seeder
{
    public function run(): void
    {
        // Create some sample doctors per clinic
        Doctor::factory()->count(10)->create();
    }
}
