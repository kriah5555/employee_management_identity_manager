<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = [
            ['username' => 'admin', 'password' => Hash::make('Indii_2023$')],
            ['username' => 'leonantheunis', 'password' => Hash::make('Indii_2023$')],
            ['username' => 'sylviesymons', 'password' => Hash::make('Indii_2023$')],
        ];
        // foreach($values as $value) {
        //     User::updateOrCreate(
        //         ['username' => $value['username']],
        //         $value
        //     );
        // }
        User::insert($values);
    }
}