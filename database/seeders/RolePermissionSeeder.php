<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $specialModules = [
            'TPUser' => [
                'All TPUser',
                'Registered',
                'Initial Payment',
                'Final Payment',
                'Verified',
                'Approved',
                'Rejected',
                'BlackListed',

                'View Application',
                'Reject Application',
                'Approve Application',
                'Blacklist Application',
                "Correction Send",
                "View Changes",
                "Download Files",
            ],
            'CoE' => [
                'All CoE',
                'Registered',
                'Initial Payment',
                'Final Payment',
                'Verified',
                'Approved',
                'Rejected',
                'BlackListed',

                'View Application',
                'Reject Application',
                'Approve Application',
                'Blacklist Application',
                "Correction Send",
                "View Changes",
                "Download Files",
            ],
            'Industry' => [
                'All Industry',
                'Registered',
                'Initial Payment',
                'Final Payment',
                'Verified',
                'Approved',
                'Rejected',
                'BlackListed',

                'View Application',
                'Reject Application',
                'Approve Application',
                'Blacklist Application',
                "Correction Send",
                "View Changes",
                "Download Files",
            ],
        ];


        $normalModules = [
            'Roles',
            'Employee',
            'Permissions',
            'Reports Accounts',
            'Departments',
            'Settings',
            'HR',
            'Payments',
            'Payroll'
        ];

        $crudActions = ['create', 'edit', 'update', 'read'];

        foreach ($specialModules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                        'name' => $action,
                        'guard_name' => 'web',
                        'group_name' => $module]);
            }
        }

        foreach ($normalModules as $module) {
            foreach ($crudActions as $action) {
                Permission::firstOrCreate([
                        'name' => $action,
                        'guard_name' => 'web',
                        'group_name' => $module,
                    ]);
            }
        }
    }
}
