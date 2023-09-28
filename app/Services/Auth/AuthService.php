<?php

namespace App\Services\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Users\Profiles;
use App\Models\Users\UserRoles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Helpers;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function createUser($values)
    {
        try {
            DB::beginTransaction();
            $values['username'] = generateUniqueUsername($values['username']);
            $password = array_key_exists('password', $values) ? $values['password'] : config('auth.default_user_password');
            $values['password'] = Hash::make($password);
            $user = User::create($values);
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function setActiveUserByUid($uid)
    {
        $user = User::findOrFail($uid);
        if ($user) {
            // Log in the user
            Auth::login($user);
        }
    }

}