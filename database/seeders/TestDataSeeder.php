<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User\MaritalStatus;
use App\Models\User\Gender;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maritalStatuses = [
            ['name' => 'Single', 'sort_order' => 1],
            ['name' => 'Married', 'sort_order' => 2],
            ['name' => 'Civil Partnership', 'sort_order' => 3],
            ['name' => 'Separated', 'sort_order' => 4],
            ['name' => 'Divorced', 'sort_order' => 5],
            ['name' => 'Widowed', 'sort_order' => 6],
            ['name' => 'In a Relationship', 'sort_order' => 7],
            ['name' => 'Other', 'sort_order' => 8]
        ];
        MaritalStatus::insert($maritalStatuses);
        $genders = [
            ['name' => 'Male', 'sort_order' => 1],
            ['name' => 'Female', 'sort_order' => 2],
            ['name' => 'Non-Binary', 'sort_order' => 3],
            ['name' => 'Transgender', 'sort_order' => 4],
            ['name' => 'Genderqueer', 'sort_order' => 5],
            ['name' => 'Genderfluid', 'sort_order' => 6],
            ['name' => 'Agender', 'sort_order' => 7],
            ['name' => 'Bigender', 'sort_order' => 8],
            ['name' => 'Two-Spirit', 'sort_order' => 9],
            ['name' => 'Other', 'sort_order' => 10],
            ['name' => 'Prefer not to say', 'sort_order' => 11]
        ];
        Gender::insert($genders);
    }
}