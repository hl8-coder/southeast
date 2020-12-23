<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class BonusGroupRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|unique:bonus_groups,name',
        ];
    }
}
