<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class BonusRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'front_remark' => 'nullable|string|max:1024',
        ];
    }

    public function attributes()
    {
        return [
            'front_remark' => __('request/api/bonus.front_remark'),
        ];
    }
}
