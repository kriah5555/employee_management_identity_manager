<?php

namespace App\Http\Rules;

use App\Http\Rules\ApiRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use App\Rules\PasswordFormatRule;

class CreateUserRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string|max:255',
            'password' => ['nullable', 'string', 'min:8', 'max:255', new PasswordFormatRule]
        ];
    }
    public function messages()
    {
        return [
            'username.required' => 'Username is required.',
        ];
    }
}
