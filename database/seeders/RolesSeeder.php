<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'superadmin'],
            ['name' => 'admin'],
            ['name' => 'moderator'],
            ['name' => 'customer_admin'],
            ['name' => 'hr_manager'],
            ['name' => 'manager'],
            ['name' => 'planner'],
            ['name' => 'staff'],
            ['name' => 'employee'],
        ];
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}