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
        $dob = $this->faker->dateTimeBetween('-80 years', '-1 years');
        $gender = $this->faker->randomElement(['male', 'female']);
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $civilStatuses = ['Single', 'Married', 'Divorced', 'Widowed'];
        $regions = ['Region X (Northern Mindanao)', 'NCR', 'Region III (Central Luzon)', 'Region IV-A (CALABARZON)'];
        $provinces = ['Bukidnon', 'Metro Manila', 'Pampanga', 'Laguna'];
        $cities = ['Damulog', 'Quezon City', 'San Fernando', 'Santa Rosa'];

        return [
            'clinic_id' => Clinic::factory(),
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->optional()->firstName,
            'last_name' => $this->faker->lastName,
            'date_of_birth' => $dob->format('Y-m-d'),
            'sex' => $gender,
            // Keep both 'sex' and 'gender' fields populated for compatibility
            'gender' => $gender,
            'philhealth_number' => $this->faker->optional()->bothify('##-#########-#'),
            'philhealth_id' => $this->faker->optional()->bothify('##-#########-#'),
            'patient_id' => $this->faker->unique()->numerify('################'),
            'address' => $this->faker->address,
            'city' => $this->faker->randomElement($cities),
            'province' => $this->faker->randomElement($provinces),
            'region' => $this->faker->randomElement($regions),
            'zip_code' => $this->faker->postcode,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->optional(0.7)->email,
            'civil_status' => $this->faker->randomElement($civilStatuses),
            'emergency_contact_name' => $this->faker->name,
            'emergency_contact_phone' => $this->faker->phoneNumber,
            'emergency_contact_relation' => $this->faker->randomElement(['Spouse', 'Parent', 'Sibling', 'Child', 'Friend']),
            'insurance_info' => $this->faker->optional(0.4)->passthrough([
                'provider' => $this->faker->company,
                'policy_number' => $this->faker->numerify('POL-########'),
                'coverage_type' => $this->faker->randomElement(['Basic', 'Comprehensive', 'Premium']),
            ]),
            'height' => $this->faker->numberBetween(140, 190), // cm
            'weight' => $this->faker->numberBetween(40, 120), // kg
            'blood_type' => $this->faker->randomElement($bloodTypes),
        ];
    }
}
