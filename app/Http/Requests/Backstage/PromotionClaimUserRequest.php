<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\PromotionClaimUser;

class PromotionClaimUserRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'PATCH':
                return [
                    'status' => 'required|in:' . get_validate_in_string(PromotionClaimUser::$statuses),
                ];
                break;
        }
    }
}
