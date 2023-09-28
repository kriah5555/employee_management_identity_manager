<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\{
    CountriesSeeder,
    LanguagesSeeder,
    RolesSeeder,
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
            CountriesSeeder::class,
            LanguagesSeeder::class,
            RolesSeeder::class,
            FileTypesSeeder::class,
            SuperdminSeeder::class
        ]);
    }
}