<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\{
    CountriesSeeder,
    LanguagesSeeder,
    RolesAndPermissionsSeeder,
    SuperdminSeeder,
    FileTypesSeeder
};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
                // CountriesSeeder::class,
                // LanguagesSeeder::class,
            RolesAndPermissionsSeeder::class,
                // FileTypesSeeder::class,
            SuperdminSeeder::class
        ]);
    }
}
