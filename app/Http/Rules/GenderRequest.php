<?php

namespace App\Http\Rules;

use App\Http\Rules\ApiRequest;

class GenderRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [];
        if ($this->isMethod('post') || $this->isMethod('put')) {
            $rules = [
                'sort_order' => 'required|integer'
            ];
            if ($this->isMethod('post')) {
                $rules = array_merge($rules, [
                    'name' => 'required|string|max:255',
                ]);
            } else {
                $rules = array_merge($rules, [
                    'name' => 'required|string|max:255',
                ]);
            }
        }
        return $rules;

    }
    public function messages()
    {
        return [];
    }
}