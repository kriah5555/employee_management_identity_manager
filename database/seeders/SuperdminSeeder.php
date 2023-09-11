<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperdminSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['username' => 'admin', 'password' => 'Indii_2023$'],
            ['username' => 'leonantheunis', 'password' => 'Indii_2023$'],
            ['username' => 'sylviesymons', 'password' => 'Indii_2023$'],
        ];

        foreach ($users as $user) {
            // Check if the user already exists by username
            if (!User::where('username', $user['username'])->exists()) {
                // Create the user
                User::create([
                    'username' => $user['username'],
                    'password' => Hash::make($user['password']),
                ]);
            }
        }
    }
}