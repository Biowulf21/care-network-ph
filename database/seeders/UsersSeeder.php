<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Models\Clinic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // ensure roles exist
        $super = Role::firstWhere('name', 'superadmin');
        $adminRole = Role::firstWhere('name', 'admin');
        $delegateRole = Role::firstWhere('name', 'delegate');

        // create superadmin
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $superadmin->roles()->syncWithoutDetaching([$super->id]);

        // for each organization create an admin and a delegate
        Organization::all()->each(function (Organization $org) use ($adminRole, $delegateRole) {
            $adminEmail = Str::slug($org->name).'@example.com';
            $admin = User::firstOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => $org->name.' Admin',
                    'password' => bcrypt('password'),
                    'organization_id' => $org->id,
                    'email_verified_at' => now(),
                ]
            );
            $admin->roles()->syncWithoutDetaching([$adminRole->id]);

            // create a delegate and assign it to a random clinic within the organization
            $delegateEmail = Str::slug($org->name).'.delegate@example.com';
            $clinic = Clinic::where('organization_id', $org->id)->inRandomOrder()->first();

            $delegate1 = User::firstOrCreate(
                ['email' => $delegateEmail],
                [
                    'name' => $org->name.' Delegate',
                    'password' => bcrypt('password'),
                    'organization_id' => $org->id,
                    'clinic_id' => $clinic?->id,
                    'email_verified_at' => now(),
                ]
            );
            $delegate1->roles()->syncWithoutDetaching([$delegateRole->id]);
        });
    }
}
