<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\CompanyBankAccount;
use App\Models\PgAccount;
use App\Rules\GtZeroRule;

class PgAccountRequest extends Request
{
   /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
   public function rules()
   {
        switch ($this->getRequestMethod()) {
            case 'remark':
                return [
                    'remark' => 'required|string|max:2048',
                ];
                break;
            case 'adjust':
                return [
                    'account_id' => 'required|integer|exists:pg_accounts,id',
                    'is_income'  => 'required|boolean',
                    'amount'     => [
                        'required',
                        'numeric',
                        new GtZeroRule()
                    ],
                    'fee' => [
                        'nullable',
                    ],
                    'txn_id' => 'sometimes|nullable|integer|exists:deposits,order_no',
                    'remark' => 'required|string|max:2048',
                ];
                break;

            case 'internalTransfer':
                return [
                    'from_account_id' => 'required|integer|exists:pg_accounts,id',
                    'to_account_id'   => 'required|integer|exists:company_bank_accounts,id',
                    'amount'    => [
                        'required',
                        'numeric',
                        new GtZeroRule()
                    ],
                    'fee' => [
                        'nullable',
                    ],
                    'remark' => 'required|string|max:2048',
                ];
                break;
            case 'update':
                return [
                    'customer_id'               => 'nullable|string',
                    'username'                  => 'nullable|string',
                    'password'                  => 'nullable|string',
                    'email'                     => 'nullable|email',
                    'email_password'            => 'nullable|string',
                    'otp'                       => 'nullable|integer|in:' . get_validate_in_string(PgAccount::$otps),
                    'remark'                    => 'required|string|max:2048',
                ];
                break;
        }
   }
}
