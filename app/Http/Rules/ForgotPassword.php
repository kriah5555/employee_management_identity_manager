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
                'email' => 'required|email|exists:user_contact_details',
                'username' => 'required|exists:users',
            ];
        } else {
            $rules = [
                'otp' => ['required','string',Rule::exists('reset_code_passwords','code')],
                'new_password' => 'required|string|min:8',
                'confirm_new_password'=>'required|string|min:8',
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'username.required' => 'username is required.',
            'username.exists' => 'username not found.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.exists' => 'Email not found.',
            'otp.required' => 'OTP is required.',
            'otp.string' => 'OTP must be a string.',
            'otp.exists' => 'Invalid OTP.',
            'new_password.required' => 'Password is required.',
            'new_password.string' => 'Password must be a string.',
            'new_password.min' => 'Password should be a minimum of eight (8) characters.',

        ];
    }


    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => implode(' ', $errors),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
