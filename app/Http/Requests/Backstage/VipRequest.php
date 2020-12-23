<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class VipRequest extends Request
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
                    'level'         => 'required|integer|unique:vips',
                    'name'          => 'required|string',
                    'display_name'  => 'required|string',
                    'rule'          => 'required|integer|min:1',
                    'remark'        => 'nullable|string',
                ];
                break;

            case 'PATCH':
                return [
                    'level'         => 'integer|unique:vips',
                    'name'          => 'string',
                    'display_name'  => 'string',
                    'rule'          => 'integer|min:1',
                    'remark'        => 'nullable|string',
                ];
                break;
        }
    }
}
