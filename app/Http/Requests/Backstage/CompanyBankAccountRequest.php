<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\CompanyBankAccount;
use App\Models\CompanyBankAccountTransaction;
use App\Rules\GtZeroRule;

class CompanyBankAccountRequest extends Request
{
   /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
   public function rules()
   {
        switch ($this->getRequestMethod()) {
            case 'store':
                return [
                    'account_name'              => 'required|string',
                    'account_no'                => 'required|min:7|max:22',
                    'payment_group_id'          => 'required|exists:payment_groups,id',
                    'type'                      => 'required|in:' . get_validate_in_string(CompanyBankAccount::$types),
                    'bank_id'                   => 'required|exists:banks,id',
                    'branch'                    => 'required|string',
                    'province'                  => 'required|string',
                    'app_related'               => 'nullable|in:' . get_validate_in_string(CompanyBankAccount::$appRelates),
                    'first_balance'             => 'nullable|numeric|min:0',
                    'balance'                   => 'nullable|numeric|min:0',
                    'user_name'                 => 'required|string',
                    'password'                  => 'required|string',
                    'phone'                     => 'nullable|string',
                    'phone_asset'               => 'nullable|string',
                    'safe_key_pass'             => 'nullable|string|max:255',
                    'otp'                       => 'nullable|integer|in:' . get_validate_in_string(CompanyBankAccount::$otps),
                    'min_balance'               => 'nullable|numeric|min:0',
                    'max_balance'               => 'nullable|numeric|min:0',
                    'daily_fund_out_limit'      => 'nullable|numeric|min:0',
                    'daily_fund_in_limit'       => 'nullable|numeric|min:0',
                    'daily_transaction_limit'   => 'nullable|numeric|min:0|max:65535',
                ];
                break;
            case 'update':
                return [
                    'account_name'              => 'nullable|string',
                    'account_no'                => 'nullable|min:7|max:22',
                    'payment_group_id'          => 'nullable|exists:payment_groups,id',
                    'type'                      => 'nullable|in:' . get_validate_in_string(CompanyBankAccount::$types),
                    'branch'                    => 'nullable|string',
                    'province'                  => 'nullable|string',
                    'app_related'               => 'nullable|in:' . get_validate_in_string(CompanyBankAccount::$appRelates),
                    'password'                  => 'nullable|string',
                    'phone'                     => 'nullable|string',
                    'phone_asset'               => 'nullable|string',
                    'safe_key_pass'             => 'nullable|string|max:255',
                    'otp'                       => 'nullable|integer|in:' . get_validate_in_string(CompanyBankAccount::$otps),
                    'min_balance'               => 'nullable|numeric|min:0',
                    'max_balance'               => 'nullable|numeric|min:0',
                    'daily_fund_out_limit'      => 'nullable|numeric|min:0',
                    'daily_fund_in_limit'       => 'nullable|numeric|min:0',
                    'daily_transaction_limit'   => 'nullable|numeric|min:0|max:65535',
                    'remark'                    => 'required|string|max:2048',
                    'status'                    => 'nullable|integer|in:' . get_validate_in_string(CompanyBankAccount::$statuses),
                ];
                break;
            case 'remark':
                return [
                    'remark' => 'required|string|max:2048',
                ];
                break;
            case 'adjust':
                return [
                    'account_id' => 'required|integer|exists:company_bank_accounts,id',
                    'is_income'  => 'required|boolean',
                    'amount'     => [
                        'required',
                        'numeric',
                        new GtZeroRule()
                    ],
                    'fee'    => 'required|numeric|min:0',
                    'reason' => 'required|integer|in:' . get_validate_in_string(CompanyBankAccountTransaction::$reasons),
                    'remark' => 'required|string|max:2048',
                ];
                break;

            case 'internalTransfer':
                return [
                    'from_account_id' => 'required|integer|exists:company_bank_accounts,id',
                    'to_account_id'   => 'required|integer|exists:company_bank_accounts,id|different:from_account_id',
                    'amount'    => [
                        'required',
                        'numeric',
                        new GtZeroRule()
                    ],
                    'fee'    => 'required|numeric|min:0',
                    'remark' => 'required|string|max:2048',
                ];
                break;
            case 'bufferTransfer':
                return [
                    'account_id' => 'required|integer|exists:company_bank_accounts,id',
                    'is_income'  => 'required|boolean',
                    'amount'     => [
                        'required',
                        'numeric',
                        new GtZeroRule()
                    ],
                    'remark' => 'required|string|max:2048',
                ];
                break;
        }
   }
}
