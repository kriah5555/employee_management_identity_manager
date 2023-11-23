<?php

namespace App\Http\Rules;

use App\Http\Rules\ApiRequest;
// use Illuminate\Contracts\Validation\Validator;
// use Illuminate\Http\JsonResponse;
// use Illuminate\Validation\Rule;


class CreateEditEmployee extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {

        return [
            // 'user_id' => 'required|exists:App\Models\User,id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'gender_id' => 'required|numeric',
            'birth_date' => 'required|date|date_format:Y-m-d|before:today',
            'birth_place' => 'required|string',
            'marital_status' => 'required|numeric',
            'street' => 'required',
            'house_no' => 'required',
            'postal_code' => 'required|numeric',
            'country' => 'required',
            'nationality_id' => 'required',
            'bank_number' => 'required',
            'dependent_spouse' => 'required|numeric',
            'mobile' => 'required',
            'email' => 'required|email:dns,spoof,rfc',
            'rsz_number' => 'required|numeric',
            'join_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:tomorrow',
            'language_id' => 'required',
            'childrens_count' => 'required|numeric',
            'current_user_id' => 'nullable|numeric'
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'User id is required',
            'user_id.exists' => 'User not exists',
            'first_name.required' => 'First name is required',
            'first_name.string' => 'First name details are wrong',
            'last_name.required' => 'Last name is required',
            // 'rsz_number.exists' => 'RSZ number already exists, please copy the employee details'
        ];
    }
}
