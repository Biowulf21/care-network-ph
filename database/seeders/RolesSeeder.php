<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            ['name' => 'superadmin', 'display_name' => 'Super Administrator'],
            ['name' => 'admin', 'display_name' => 'Organization Admin'],
            ['name' => 'delegate', 'display_name' => 'Delegate'],
        ])->each(function ($role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        });
    }
}
