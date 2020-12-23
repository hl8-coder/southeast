<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class RewardRequest extends Request
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
                    'level'         => 'required|integer|unique:rewards',
                    'rule'          => 'required|integer|min:1',
                    'remark'        => 'nullable|string',
                ];
                break;

            case 'PATCH':
                return [
                    'level'         => 'integer|unique:rewards',
                    'rule'          => 'integer|min:1',
                    'remark'        => 'nullable|string',
                ];
                break;
        }
    }
}
