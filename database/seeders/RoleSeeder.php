<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // End User Roles
        Role::create(['name' => 'TP User']);
        Role::create(['name' => 'Partner']);
        Role::create(['name' => 'CoE']);

        // Admin Roles
        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Account']);
        Role::create(['name' => 'HR']);
        Role::create(['name' => 'QA Dep']);
    }



}
