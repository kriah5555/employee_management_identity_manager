<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FileTypes;

class FileTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = [
            ['name' => 'profile'],
            ['name' => 'id_card'],
        ];
        foreach($values as $value) {
            FileTypes::create($value);
        }  
    }
}
