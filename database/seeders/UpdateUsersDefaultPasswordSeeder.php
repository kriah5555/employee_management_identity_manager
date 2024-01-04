<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User\User;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\DB;

class UpdateUsersDefaultPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();
            $users = User::with('userBasicDetails')->get();
            foreach ($users as $user) {
                $name = $user->userBasicDetails->first_name;
                $dateOfBirth = $user->userBasicDetails->date_of_birth;
                if ($name && $dateOfBirth) {
                    $password = ucfirst($user->userBasicDetails->first_name . date('dmY', strtotime($user->userBasicDetails->date_of_birth)));
                    $user->password = Hash::make($password);
                    $user->save();
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
