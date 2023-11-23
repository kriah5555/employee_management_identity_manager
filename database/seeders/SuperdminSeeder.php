<?php

namespace Database\Seeders;

use App\Models\User\UserContactDetails;
use App\Models\User\UserFamilyDetails;
use Illuminate\Database\Seeder;
use App\Models\User\User;
use Illuminate\Support\Facades\Hash;
use App\Models\User\UserBasicDetails;
use App\Models\User\UserAddress;

class SuperdminSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['username' => 'admin', 'first_name' => 'admin', 'last_name' => 'admin', 'password' => 'Indii_2023$', 'role' => 'superadmin'],
            ['username' => 'leonantheunis', 'first_name' => 'Leon', 'last_name' => 'Antheunis', 'password' => 'Indii_2023$', 'role' => 'admin'],
            ['username' => 'sylviesymons', 'first_name' => 'Sylvie', 'last_name' => 'Symons', 'password' => 'Indii_2023$', 'role' => 'admin'],
        ];

        foreach ($users as $user) {
            // Check if the user already exists by username
            if (!User::where('username', $user['username'])->exists()) {
                // Create the user
                $userObj = User::create([
                    'username' => $user['username'],
                    'password' => Hash::make($user['password']),
                ]);
                $userObj->assignRole($user['role']);
                $userBasicDetails['user_id'] = $userAddressDetails['user_id'] = $userContactDetails['user_id'] = $userFamilyDetails['user_id'] = $userObj->id;
                $userBasicDetails['first_name'] = $user['first_name'];
                $userBasicDetails['last_name'] = $user['last_name'];
                UserBasicDetails::create($userBasicDetails);
                UserAddress::create($userAddressDetails);
                UserContactDetails::create($userContactDetails);
                UserFamilyDetails::create($userFamilyDetails);
            }
        }
    }
}
