<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Withdrawal;
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
        switch ($this->getRequestMethod()) {
            case 'remark':
                return [
                    'remark'    => 'required|string|max:1024',
                ];
                break;
            case 'hold':
                return [
                    'hold_reason' => 'required|integer|in:' . implode(',', array_keys(Withdrawal::$holdReasons)),
                ];
                break;
            case 'reject':
                return [
                    'reject_reason' => 'required|integer|in:' . implode(',', array_keys(Withdrawal::$rejectReasons)),
                ];
                break;
            case 'escalate':
                return [
                    'escalate_reason' => 'required|integer|in:' . implode(',', array_keys(Withdrawal::$escalateReasons)),
                ];
                break;
            case 'addRecords':
                return [
                    'records'                              => 'array',
                    'records.*.company_bank_account_code'  => 'required|exists:company_bank_accounts,code',
                    'records.*.amount'                     => 'required|numeric|min:0',
                    'records.*.fee'                        => 'required|numeric|min:0|max:20',
                ];
                break;
            case 'image':
                return [
                    'image' => 'required|image|mimes:jpeg,bmp,png,gif',
                ];
                break;
            case 'index':
                return [
                    'filter.user_name' => 'required|exists:users,name'
                ];
                break;
        }
    }

    public function messages()
    {
        $messages = [
            'filter.user_name.exists' => 'The member dosen\'t exists!',
        ];
        return $messages;
    }


    public function attributes()
    {
        $attributes = [
            'filter.user_name' => 'member\'s name',
        ];
        return $attributes;
    }
}
