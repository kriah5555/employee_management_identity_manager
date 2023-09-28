<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// use App\Models\Roles;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'superadmin', 'guard_name' => 'api'],
            ['name' => 'admin', 'guard_name' => 'api'],
            ['name' => 'moderator', 'guard_name' => 'api'],
            ['name' => 'customer_admin', 'guard_name' => 'api'],
            ['name' => 'hr_manager', 'guard_name' => 'api'],
            ['name' => 'manager', 'guard_name' => 'api'],
            ['name' => 'planner', 'guard_name' => 'api'],
            ['name' => 'staff', 'guard_name' => 'api'],
            ['name' => 'employee', 'guard_name' => 'api'],
        ];
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
