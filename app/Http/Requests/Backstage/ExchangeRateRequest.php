<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class ExchangeRateRequest extends Request
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
                    'currency_code_from'        => 'required|exists:currencies,code',
                    'currency_code_to'          => 'required|exists:currencies,code',
                    'conversion_value'          => 'required|numeric|min:0',
                    'inverse_conversion_value'  => 'required|numeric|min:0',
                ];
                break;

            case 'PATCH':
                return [
                    'currency_code_from'        => 'nullable|exists:currencies,code',
                    'currency_code_to'          => 'nullable|exists:currencies,code',
                    'conversion_value'          => 'nullable|numeric|min:0',
                    'inverse_conversion_value'  => 'nullable|numeric|min:0',
                ];
                break;
        }
    }
}
