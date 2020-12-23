<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;

class UserRebatePrizeRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_rebate_prize_ids'     => 'required|array',
            'user_rebate_prize_ids.*'   => 'required|exists:user_rebate_prizes,id'
        ];
    }
}
