<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class PointRuleRequest extends Request
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
                    'currency'  => 'required',
                    'rule'      => 'required|integer|min:1',
                ];
                break;

            case 'PATCH':
                return [
                    'currency'  => 'string',
                    'rule'      => 'integer|min:1',
                ];
                break;
        }

    }
}
