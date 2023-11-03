<?php

namespace App\Http\Rules;

use App\Http\Rules\ApiRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ForgotPassword extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $path = $this->getPathInfo(); // Get the path of the current URL

        if (str_contains($path, 'employee/forgot-password')) {
            $rules = [
                'email' => 'required|email|exists:users',
            ];
        } else {
            $rules = [
                'code' => 'required|string|exists:reset_code_passwords',
                'new_password' => 'required|string|min:8',
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.exists' => 'Email not found.',
            'code.required' => 'OTP is required.',
            'code.string' => 'OTP must be a string.',
            'code.exists' => 'Invalid OTP.',
            'new_password.required' => 'Password is required.',
            'new_password.string' => 'Password must be a string.',
            'new_password.min' => 'Password should be a minimum of eight (8) characters.',
        ];
    }
}
