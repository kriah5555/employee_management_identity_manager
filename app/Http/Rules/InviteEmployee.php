<?php

namespace App\Http\Rules;

use App\Http\Rules\ApiRequest;
// use Illuminate\Contracts\Validation\Validator;
// use Illuminate\Http\JsonResponse;
// use Illuminate\Validation\Rule;


class InviteEmployee extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // 'username' => 'required|string|max:255',

        return [
            'mail' => 'required|email|unique:user_basic_details,email',
            'invite_role' => 'required|numeric',
            'invite_by' => 'required|numeric',
            'company_id' => 'required|numeric',
        ];
    }
    public function messages()
    {
        return [
            'mail.email' => 'The email must be a valid email address.',
            'mail.required' => 'The email field is required.',
            'mail.exists' => 'This email does not exist in the roles table.'
        ];
    }
}
