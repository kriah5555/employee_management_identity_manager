<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MaritalStatus;

class MaritalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = [
            ['name' => 'Single', 'sort_order' => 1],
            ['name' => 'Married', 'sort_order' => 2],
            ['name' => 'Divorced', 'sort_order' => 3],
            ['name' => 'Widowed', 'sort_order' => 4],
            ['name' => 'Separated', 'sort_order' => 5],
        ];

        foreach ($values as $value) {
            MaritalStatus::create($value);
        }
    }
}