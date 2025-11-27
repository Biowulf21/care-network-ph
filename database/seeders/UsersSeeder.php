<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Organization;
use App\Models\User;
use App\Models\Role;
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
            $admin = User::create([
                'name' => $org->name . ' Admin',
                'email' => Str::slug($org->name) . '@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            $admin->roles()->syncWithoutDetaching([$adminRole->id]);

            // a couple delegates
            $delegate1 = User::create([
                'name' => $org->name . ' Delegate',
                'email' => Str::slug($org->name) . '.delegate@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            $delegate1->roles()->syncWithoutDetaching([$delegateRole->id]);
        });
    }
}
