<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClinicFactory extends Factory
{
    protected $model = Clinic::class;

    public function definition(): array
    {
        $faker = $this->faker;

        return [
            'organization_id' => Organization::factory(),
            'name' => $faker->company.' Clinic',
            'code' => strtoupper($faker->unique()->bothify('CLN###')),
            'address' => $faker->address,
            'phone' => $faker->phoneNumber,
        ];
    }
}
