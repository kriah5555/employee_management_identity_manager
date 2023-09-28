<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee\Gender;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = [
            ['name' => 'Male'],
            ['name' => 'Female'],
            ['name' => 'Others'],
        ];
        foreach ($values as $value) {
            Gender::insert($value);
        }
    }
}