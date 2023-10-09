<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gender;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = [
            ['name' => 'Male', 'sort_order' => 1],
            ['name' => 'Female', 'sort_order' => 2],
            ['name' => 'Others', 'sort_order' => 3],
        ];
        foreach ($values as $value) {
            Gender::insert($value);
        }
    }
}