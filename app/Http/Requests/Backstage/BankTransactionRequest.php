<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Rules\GtZeroRule;

class BankTransactionRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->getRequestMethod()) {
            case 'updateCredit':
                return [
                    'credit'    => 'required|numeric|min:0',
                    'remark'    => 'required|string|max:255',
                ];
                break;
            case 'importExcel':
                return [
                    'excel'             => 'required|file',
                    'fund_in_account'   => 'required|exists:company_bank_accounts,code',
                    'is_force'          => 'nullable|boolean',
                ];
                break;
            case 'importText':
                return [
                    'text'              => 'required|string',
                    'fund_in_account'   => 'required|exists:company_bank_accounts,code',
                    'last_balance'      => 'nullable|string',
                    'is_force'          => 'nullable|boolean',

                ];
                break;
            case 'destroyDuplicateTransactions':
                return [
                    'fund_in_account'   => 'required|exists:company_bank_accounts,code',
                    'debit'             => 'required|numeric',
                    'credit'            => 'required|numeric',
                    'balance'           => 'required|numeric',
                    'transaction_date'  => 'required|date',
                    'description'       => 'required|string',
                ];
                break;
        }
    }
}
