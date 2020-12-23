<?php

namespace App\Http\Requests\Backstage;

use App\Models\CompanyBankAccount;
use App\Models\Deposit;
use App\Http\Requests\Request;
use App\Models\PaymentPlatform;
use App\Models\User;
use Illuminate\Validation\Rule;

class DepositRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $methodName = $this->getRequestMethod();

        switch ($methodName) {
            case 'updateAmountDetail': # 更改金额细节
                $paymentPlatformCodes = PaymentPlatform::getAll()->pluck('code')->toArray();
                return [
                    'fund_in_account'   => 'required|string|in:' . implode(',' , $paymentPlatformCodes),
                    'receive_amount'    => 'required|numeric|min:0',
                    'arrival_amount'    => 'required|numeric|min:0',
                    'bank_fee'          => 'required|numeric|min:0',
                    'reimbursement_fee' => 'required|numeric|min:0',
                ];
                break;
            case 'receipt': #上传凭证
                return [
                    'image' => 'required|image|mimes:jpeg,bmp,png,gif',
                ];
                break;
            case 'reject':  # 拒绝
                return [
                    'reject_reason' => 'nullable|in:0,' . implode(',', array_keys(Deposit::$rejectReasons)),
                ];
                break;
            case 'hold': # 保留
                return [
                    'hold_reason' => 'required|in:' . implode(',', array_keys(Deposit::$holdReasons)),
                ];
                break;
            case 'approvePartial': # 请求部份上分
                return [
                    'partial_amount' => 'required|numeric|min:0',
                ];
                break;
            case 'lose': # 遗失
                return [
                    'remark' => 'required|string',
                ];
                break;
            case 'index': # 充值列表
                $isAgent = (boolean)$this->input('filter.is_agent', false);
                if ($isAgent) {
                    return [
                        'filter.user_name' => ['required', function ($attribute, $value, $fail) {
                            $exists = app(User::class)->where('is_agent', true)->where('name', $value)->exists();
                            if (!$exists) {
                                $fail('The affiliate dosen\'t exists!');
                            }
                        }],
                    ];
                } else {
                    return [
                        'filter.user_name' => ['required', function ($attribute, $value, $fail) {
                            $exists = app(User::class)->where('is_agent', false)->where('name', $value)->exists();
                            if (!$exists) {
                                $fail('The member dosen\'t exists!');
                            }
                        }],
                    ];
                }
                break;
            case 'updateRemarks': # 更改备注
                return [
                    'remarks' => 'required|string',
                ];
                break;
            case 'revertAction': # 取消请求
            case 'approveAdvanceCredit': # 完整上分
            case 'approvePartialAdvanceCredit': # 部份上分
            case 'remarkIndex': # 获取会员最近两年的remark
            case 'releaseHold': # 取消保留
            case 'requestAdvance': # 上分类型选择
            case 'bankTransaction': # 银行交易记录详情
            case 'match':   # 充值领取银行交易记录
            case 'unmatch': # 取消充值领取银行交易记录
            case 'approveAdv': # 请求全额上分
            case 'cancel':      # 取消
            case 'approve': # 批准
            case 'approveChanges': # 二次批准
            case 'receiptDelete': # 删除凭证
            case 'show':    # 充值订单详情
            case 'openDeposit': # 所有充值订单
            case 'fastDeposit': # 网银充值订单
            case 'gateway':     # 三方充值订单
            case 'advanceCredit': # 可上分充值订单
            case 'byUser':      # 会员充值记录
            case 'callBack':
                return [];
                break;
        }
        return [];
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
