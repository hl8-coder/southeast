<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;
use App\Models\UserBankAccount;

class UserBankAccountRequest extends Request
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
                    'is_preferred' => 'required|boolean',
                    'bank_id'      => 'required|exists:banks,id',
                    'province'     => 'nullable|string',
                    'city'         => 'nullable|string',
                    'branch'       => 'required|string',
                    'account_name' => 'required|string',
                    'account_no'   => [
                        'required',
                        'numeric',
                        function ($attribute, $value, $fail) {

                            $this->checkAccountNoLength($fail, $value);

                            # 检查是否存在active的该银行卡号
                            if (UserBankAccount::isAccountExists($value)) {
                                $fail(__('request/api/userbankaccount.account_is_exists'));
                            }

                            # 检查会员是否超过规定数量银行卡
                            if ($this->user()->isReachBankAccountLimit()) {
                                $fail(__('request/api/userbankaccount.bank_account_is_reach_limit'));
                            }


                        },
                    ],
                ];
                break;
            case 'storeBank':
                return [
                    'bank_id'      => 'required|exists:banks,id',
                    'province'     => 'required|string',
                    'city'         => 'required|string',
                    'branch'       => 'required|string',
                    'account_name' => 'required|string',
                    'account_no'   => [
                        'required',
                        function ($attribute, $value, $fail) {

                            $this->checkAccountNoLength($fail, $value);

                            # 检查是否存在active的该银行卡号
                            if (UserBankAccount::isAccountExists($value)) {
                                $fail(__('request/api/userbankaccount.account_is_exists'));
                            }

                            # 检查会员是否超过规定数量银行卡
                            if ($this->user()->isReachBankAccountLimit()) {
                                $fail(__('request/api/userbankaccount.bank_account_is_reach_limit'));
                            }
                        },
                    ],
                ];
                break;

            case 'update':
                return [
                    'bank_id'       => 'nullable|exists:banks,id',
                    'province'      => 'nullable|string',
                    'city'          => 'nullable|string',
                    'branch'        => 'nullable|string',
                    'is_preferred'  => 'nullable|integer|in:0,1',
                ];
                break;

            case 'updateBank':
                return [
                    'bank_id'  => 'exists:banks,id',
                    'province' => 'string',
                    'city'     => 'string',
                    'branch'   => 'string',
                    'account_name' => 'string',
                    'account_no'   => [
                        'nullable',
                        function ($attribute, $value, $fail) {
                            $this->checkAccountNoLength($fail, $value);
                        },
                    ],
                ];
                break;
        }
    }

    public function checkAccountNoLength($fail, $value)
    {
        # 检查字符长度限制
        # VND  6~16
        # THB  10~12
        $currency = $this->user()->currency;
        $len = strlen($value);
        if ('VND' == $currency && ($len > 16 || $len < 6)) {
            return $fail(__('request/api/userbankaccount.vnd_account_no_exceed_the_limit'));
        } elseif ('THB' == $currency && ($len > 12 || $len < 10)) {
            return $fail(__('request/api/userbankaccount.thb_account_no_exceed_the_limit'));
        }
    }

    public function attributes()
    {
        return [
            'is_preferred' => __('request/api/userbankaccount.is_preferred'),
            'bank_id'      => __('request/api/userbankaccount.bank_id'),
            'province'     => __('request/api/userbankaccount.province'),
            'city'         => __('request/api/userbankaccount.city'),
            'branch'       => __('request/api/userbankaccount.branch'),
            'account_name' => __('request/api/userbankaccount.account_name'),
            'account_no'   => __('request/api/userbankaccount.account_no'),
        ];
    }
}
