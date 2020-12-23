<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class AffiliateRequestApproveRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'commission_setting' => 'required',
            'commission_setting.*.tier' => 'required|integer',
            'commission_setting.*.title' => 'required|string',
            'commission_setting.*.value' => 'required|integer',
            'commission_setting.*.profit' => 'required|integer',
        ];
    }
}
