<?php

use App\Models\User;
  
/**
 * Write code on Method
 *
 * @return response()
 */
if (!function_exists('generateUniqueUsername')) {
    function generateUniqueUsername($username)
    {
        $newUsername = $username;
        $counter = 1;

        while (User::where('username', $newUsername)->exists()) {
            $newUsername = $username . $counter;
            $counter++;
        }

        return $newUsername;
    }
}
