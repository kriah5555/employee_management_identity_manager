<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Languages;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = [
            ['name' => 'English', 'code' => 'en'],
            ['name' => 'Dutch', 'code' => 'nl'],
            ['name' => 'French', 'code' => 'fr'],
        ];
        foreach ($values as $value) {
            Languages::create($value);
        }
    }
}
