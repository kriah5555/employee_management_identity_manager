<?php

namespace App\Http\Rules;

use App\Http\Rules\ApiRequest;

class GenerateAccessTokenRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'refresh_token' => 'required|string'
        ];
    }
    public function messages()
    {
        return [
            'refresh_token.required' => 'Refresh token is required.'
        ];
    }
}