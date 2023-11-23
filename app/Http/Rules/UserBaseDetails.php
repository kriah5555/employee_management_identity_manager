<?php

namespace App\Http\Rules;

use App\Http\Rules\ApiRequest;
// use Illuminate\Contracts\Validation\Validator;
// use Illuminate\Http\JsonResponse;
// use Illuminate\Validation\Rule;


class UserBaseDetails extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name',
            'last_name',
            'email',
            'mobile',
            'rsz_number',
            'birth_date',
            'birth_place',
            'bank_account',
            'gender_id',
            'nationality_id',
            'language_id',
            'status'
        ];
    }
    public function messages()
    {
        return [
            
        ];
    }
}
