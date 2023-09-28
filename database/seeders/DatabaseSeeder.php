<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use Database\Seeders\SuperdminSeeder;
// use Database\Seeders\RolesSeeder;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\{
    CountriesSeeder,
    GenderSeeder,
    LanguagesSeeder,
    MaritalStatusSeeder,
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
        $this->call(RolesSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(SuperdminSeeder::class);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            CountriesSeeder::class,
            GenderSeeder::class,
            LanguagesSeeder::class,
            MaritalStatusSeeder::class,
            RolesSeeder::class,
            FileTypesSeeder::class,
            SuperdminSeeder::class
        ]);
    }
}