<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            ['name' => 'Configuration'],
            ['name' => 'Planning'],
            ['name' => 'Employees'],
            ['name' => 'Manage configuration'],
            ['name' => 'View configuration details'],
            ['name' => 'Edit configuration'],
            ['name' => 'Manage companies'],
            ['name' => 'View company details'],
            ['name' => 'Edit company details'],
            ['name' => 'View contract types'],
            ['name' => 'Edit contract types'],
            ['name' => 'View employee types'],
            ['name' => 'Edit employee types'],
            ['name' => 'View sectors'],
            ['name' => 'Edit sectors'],
            ['name' => 'View functions'],
            ['name' => 'Edit functions'],
            ['name' => 'View minimum salaries'],
            ['name' => 'Edit minimum salaries'],
            ['name' => 'View holiday codes'],
            ['name' => 'Edit holiday codes'],
        ];
        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        $superadminRole = Role::where('name', 'superadmin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        foreach ($permissions as $permission) {
            $superadminRole->givePermissionTo($permission['name']);
            $adminRole->givePermissionTo($permission['name']);
        }
    }
}