<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CurrencyRequest extends Request
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
                    'name'                             => 'required|string',
                    'code'                             => 'required|string|unique:currencies',
                    'preset_language'                  => 'required|string|exists:languages,code',
                    'country'                          => 'required|string|exists:currencies,country',
                    'country_code'                     => 'required|string|exists:currencies,country_code',
                    'sort'                             => 'nullable|integer|min:0',
                    'is_remove_three_zeros'            => 'nullable|boolean',
                    'deposit_second_approve_amount'    => 'nullable|numeric',
                    'withdrawal_second_approve_amount' => 'nullable|numeric',
                    'bank_account_verify_amount'       => 'nullable|numeric',
                    'info_verify_prize_amount'         => 'nullable|numeric',
                    'max_deposit'                      => 'nullable|numeric',
                    'min_deposit'                      => 'nullable|numeric',
                    'max_withdrawal'                   => 'nullable|numeric',
                    'min_withdrawal'                   => 'nullable|numeric',
                    'max_daily_withdrawal'             => 'nullable|numeric',
                    'min_transfer'                     => 'nullable|numeric',
                    'max_transfer'                     => 'nullable|numeric',
                    'commission'                       => 'nullable|numeric',
                    'payout_comm_mini_limit'           => 'nullable|string',
                    'deposit_pending_limit'            => 'nullable|numeric',
                    'withdrawal_pending_limit'         => 'nullable|numeric',
                    'status'                           => 'nullable|boolean',
                ];
                break;

            case 'PATCH':
                return [
                    'name'                             => 'nullable|string',
                    'code'                             => [
                        'nullable',
                        'string',
                        Rule::unique('currencies')->ignore($this->route('currency')->id),
                    ],
                    'preset_language'                  => 'nullable|string|exists:languages,code',
                    'sort'                             => 'nullable|integer|min:0',
                    'is_remove_three_zeros'            => 'nullable|boolean',
                    'deposit_second_approve_amount'    => 'nullable|numeric',
                    'withdrawal_second_approve_amount' => 'nullable|numeric',
                    'bank_account_verify_amount'       => 'nullable|numeric',
                    'info_verify_prize_amount'         => 'nullable|numeric',
                    'max_deposit'                      => 'nullable|numeric',
                    'min_deposit'                      => 'nullable|numeric',
                    'max_withdrawal'                   => 'nullable|numeric',
                    'min_withdrawal'                   => 'nullable|numeric',
                    'max_daily_withdrawal'             => 'nullable|numeric',
                    'min_transfer'                     => 'nullable|numeric',
                    'max_transfer'                     => 'nullable|numeric',
                    'commission'                       => 'nullable|numeric',
                    'payout_comm_mini_limit'           => 'nullable|string',
                    'deposit_pending_limit'            => 'nullable|numeric',
                    'withdrawal_pending_limit'         => 'nullable|numeric',
                    'status'                           => 'nullable|boolean',
                ];
                break;
        }
    }
}
