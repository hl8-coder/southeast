<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class UserBonusPrizeRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'remark' => 'nullable|string|max:1024',
        ];
    }
}
