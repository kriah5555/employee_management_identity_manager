<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordFormatRule implements Rule
{
    public function passes($attribute, $value)
    {
        // Ensure the password contains at least one uppercase letter, one lowercase letter, and one symbol
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()\-_=+{};:,<.>]).+$/', $value);
    }

    public function message()
    {
        return 'The :attribute must contain at least one uppercase letter, one lowercase letter, and one symbol.';
    }
}