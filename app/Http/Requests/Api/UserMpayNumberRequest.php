<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;
use App\Models\UserMpayNumber;

class UserMpayNumberRequest extends Request
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
                    'area_code' => 'required',
                    'number'    => [
                        'required',
                        'min:8',
                        'max:22',
                        'unique:user_mpay_numbers',
                        function ($attribute, $value, $fail) {
                            # 检查会员是否超过规定数量银行卡
                            if ($this->user()->isReachMpayNumberLimit()) {
                                $fail(__('request/api/usermpaynumber.mpay_number_limit'));
                            }
                        },
                    ],
                ];
                break;
            case 'PATCH':
                return [
                    'area_code' => 'required|exists:currencies,country_code',
                    'number'    => 'required|size:10',
                ];
                break;
        }
    }

    public function attributes()
    {
        return [
            'area_code' => __('request/api/usermpaynumber.area_code'),
            'number'    => __('request/api/usermpaynumber.number'),
        ];
    }
}
