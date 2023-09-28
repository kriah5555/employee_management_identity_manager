<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use App\Models\Roles;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = [
            ['name' => 'INDII admin', 'guard_name' => 'api'],
            ['name' => 'HR', 'guard_name' => 'api'],
            ['name' => 'Sales', 'guard_name' => 'api'],
            ['name' => 'Customer', 'guard_name' => 'api'],
            ['name' => 'Manager', 'guard_name' => 'api'],
            ['name' => 'Planner', 'guard_name' => 'api'],
            ['name' => 'Employee', 'guard_name' => 'api'],
        ];
        foreach ($values as $value) {
            Role::create($value);
        }
    }
}
