<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        $faker = $this->faker;

        $dob = $faker->dateTimeBetween('-80 years', '-1 years');
        $gender = $faker->randomElement(['male', 'female']);
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $civilStatuses = ['Single', 'Married', 'Divorced', 'Widowed'];
        $regions = ['Region X (Northern Mindanao)', 'NCR', 'Region III (Central Luzon)', 'Region IV-A (CALABARZON)'];
        $provinces = ['Bukidnon', 'Metro Manila', 'Pampanga', 'Laguna'];
        $cities = ['Damulog', 'Quezon City', 'San Fernando', 'Santa Rosa'];

        return [
            'clinic_id' => Clinic::factory(),
            'first_name' => $faker->firstName,
            'middle_name' => $faker->optional()->firstName,
            'last_name' => $faker->lastName,
            'date_of_birth' => $dob->format('Y-m-d'),
            'sex' => $gender,
            // Keep both 'sex' and 'gender' fields populated for compatibility
            'gender' => $gender,
            'philhealth_number' => $faker->optional()->bothify('##-#########-#'),
            'philhealth_id' => $faker->optional()->bothify('##-#########-#'),
            'patient_id' => $faker->unique()->numerify('################'),
            'address' => $faker->address,
            'city' => $faker->randomElement($cities),
            'province' => $faker->randomElement($provinces),
            'region' => $faker->randomElement($regions),
            'zip_code' => $faker->postcode,
            'phone' => $faker->phoneNumber,
            'email' => $faker->optional(0.7)->email,
            'civil_status' => $faker->randomElement($civilStatuses),
            'emergency_contact_name' => $faker->name,
            'emergency_contact_phone' => $faker->phoneNumber,
            'emergency_contact_relation' => $faker->randomElement(['Spouse', 'Parent', 'Sibling', 'Child', 'Friend']),
            'insurance_info' => $faker->optional(0.4)->passthrough([
                'provider' => $faker->company,
                'policy_number' => $faker->numerify('POL-########'),
                'coverage_type' => $faker->randomElement(['Basic', 'Comprehensive', 'Premium']),
            ]),
            'height' => $faker->numberBetween(140, 190), // cm
            'weight' => $faker->numberBetween(40, 120), // kg
            'blood_type' => $faker->randomElement($bloodTypes),
        ];
    }
}
