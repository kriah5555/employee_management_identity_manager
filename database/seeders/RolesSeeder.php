<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Roles;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = [
            ['title' => 'INDII admin', 'key' => 'indii_admin'],
            ['title' => 'HR', 'key' => 'hr'],
            ['title' => 'Sales', 'key' => 'sales'],
            ['title' => 'Customer', 'key' => 'customer'],
            ['title' => 'Manager', 'key' => 'manager'],
            ['title' => 'Planner', 'key' => 'planner'],
            ['title' => 'Employee', 'key' => 'employee'],
        ];
        foreach ($values as $value) {
            Roles::create($value);
        }
    }
}
