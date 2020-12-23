<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\UserRisk;

class UserRiskRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'behaviour' => 'required|integer|in:' . implode(',', array_keys(UserRisk::$behaviour)),
            'risk'      => 'required|integer|in:' . implode(',', array_keys(UserRisk::$risk)),
            'remark'    => 'required|string',
            'user_id'   => 'required|exists:users,id',
        ];
    }
}
