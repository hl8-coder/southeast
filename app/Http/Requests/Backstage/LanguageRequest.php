<?php

namespace App\Http\Requests\Backstage;


use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class LanguageRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'name'  => 'required|string',
                    'code'  => 'required|string|unique:languages'
                ];
                break;

            case 'PATCH':
                return [
                    'name'  => 'string',
                    'code'  => [
                        'string',
                        Rule::unique('languages')->ignore($this->route('language')->id)
                    ]
                ];
                break;
        }
    }
}
