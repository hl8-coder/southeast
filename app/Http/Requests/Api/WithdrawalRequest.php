<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;
use App\Rules\GtZeroRule;

class WithdrawalRequest extends Request
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
                    'user_bank_account_id'  => 'required',
//                    'user_bank_account_id' => 'required|exists:user_bank_accounts,id',
                    'amount'               => [
                        'required',
                        'integer',
                        new GtZeroRule(),
                    ],
                ];
                break;
        }
    }

    public function attributes()
    {
        return [
            'user_bank_account_id' => __('request/api/withdrawal.user_bank_account_id'),
            'amount'               => __('request/api/withdrawal.amount'),
        ];
    }
}
