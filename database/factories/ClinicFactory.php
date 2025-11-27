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
        return [
            'organization_id' => Organization::factory(),
            'name' => $this->faker->company.' Clinic',
            'code' => strtoupper($this->faker->bothify('CLN###')),
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
        ];
    }
}
