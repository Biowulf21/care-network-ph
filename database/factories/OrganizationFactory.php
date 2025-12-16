<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $faker = $this->faker;

        return [
            'name' => $faker->company,
            'code' => strtoupper($faker->unique()->bothify('ORG###')),
            'address' => $faker->address,
            'phone' => $faker->phoneNumber,
        ];
    }
}
