<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class ClinicsSeeder extends Seeder
{
    public function run(): void
    {
        Organization::all()->each(function (Organization $org) {
            Clinic::factory()->count(3)->create(['organization_id' => $org->id]);
        });
    }
}
