<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\PaymentPlatform;

class PaymentGroupRequest extends Request
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
                $currency = $this->input('currency');
                return [
                    'name'                 => 'required|string|unique:payment_groups|max:4',
                    'currency'             => 'required|string|exists:currencies,code',
                    'account_code'         => 'required|array',
                    'account_code.*'       => ['required', 'string', function($attribute, $value, $fail) use ($currency){
                        $doesntExist = PaymentPlatform::query()->whereRaw('FIND_IN_SET(?,currencies)', [$currency])->where('code', $value)->doesntExist();
                        if ($doesntExist){
                            $fail('account code [' . $value . '] is incorrect.');
                        }
                    }],
                    'preset_risk_group_id' => 'nullable|integer|exists:risk_groups,id',
                    'remark'               => 'nullable|string',
                ];
                break;

            case 'PATCH':
                $currency = $this->input('currency');
                return [
                    # viet-192 不允许修改名称
                    // 'name'                 => [
                    //     'nullable',
                    //     'string',
                    //     'max:2',
                    //     Rule::unique('payment_groups')->ignore($this->route('payment_group')->id)
                    // ],
                    'currency'             => 'required|string|exists:currencies,code',
                    'account_code'         => 'nullable|array',
                    'account_code.*'       => ['required', 'string',  function($attribute, $value, $fail) use ($currency){
                        $doesntExist = PaymentPlatform::query()->whereRaw('FIND_IN_SET(?,currencies)', [$currency])->where('code', $value)->doesntExist();
                        if ($doesntExist){
                            $fail('account code [' . $value . '] is incorrect.');
                        }
                    }],
                    'preset_risk_group_id' => 'nullable|integer|exists:risk_groups,id',
                    'remark'               => 'nullable|string',
                ];
                break;
            default:
                return [];

        }
    }
}
