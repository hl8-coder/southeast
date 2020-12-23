<?php

namespace App\Transformers;

use App\Models\Bank;
use App\Models\CompanyBankAccount;
use App\Models\PaymentGroup;

/**
 * @OA\Schema(
 *   schema="CompanyBankAccount",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="公司银行卡id"),
 *   @OA\Property(property="platform_id", type="string", description="支付平台id"),
 *   @OA\Property(property="type", type="string", description="类型"),
 *   @OA\Property(property="code", type="string", description="辨识码"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="payment_group_id", type="string", description="支付组别"),
 *   @OA\Property(property="display_payment_group_id", type="string", description="支付组别显示值"),
 *   @OA\Property(property="bank_id", type="integer", description="银行id"),
 *   @OA\Property(property="bank_code", type="string", description="银行code"),
 *   @OA\Property(property="province", type="string", description="省"),
 *   @OA\Property(property="city", type="string", description="市"),
 *   @OA\Property(property="branch", type="string", description="分行"),
 *   @OA\Property(property="account_name", type="string", description="开户人姓名"),
 *   @OA\Property(property="account_no", type="string", description="开户账号"),
 *   @OA\Property(property="phone", type="string", description="电话号码"),
 *   @OA\Property(property="phone_asset", type="string", description="电话编号"),
 *   @OA\Property(property="user_name", type="string", description="登录账号"),
 *   @OA\Property(property="password", type="string", description="登录密码"),
 *   @OA\Property(property="app_related", type="string", description="关联app"),
 *   @OA\Property(property="first_balance", type="number", description="初始余额"),
 *   @OA\Property(property="safe_key_pass", type="string", description="app密码"),
 *   @OA\Property(property="otp", type="integer", description="关联密码"),
 *   @OA\Property(property="display_otp", type="string", description="关联密码显示"),
 *   @OA\Property(property="balance", type="number", description="余额"),
 *   @OA\Property(property="balance_color", type="string", description="余额超出限制颜色"),
 *   @OA\Property(property="min_balance", type="number", description="最小金额"),
 *   @OA\Property(property="max_balance", type="number", description="最大金额"),
 *   @OA\Property(property="daily_fund_out", type="number", description="日出款"),
 *   @OA\Property(property="daily_fund_out_limit", type="number", description="日出款限制"),
 *   @OA\Property(property="daily_fund_out_color", type="string", description="日出款超出限制颜色"),
 *   @OA\Property(property="daily_fund_in", type="number", description="日存款"),
 *   @OA\Property(property="daily_fund_in_limit", type="number", description="日存款限制"),
 *   @OA\Property(property="daily_fund_in_color", type="string", description="日存款超出限制颜色"),
 *   @OA\Property(property="daily_transaction", type="number", description="日交易次数"),
 *   @OA\Property(property="daily_transaction_limit", type="number", description="日交易次数限制"),
 *   @OA\Property(property="daily_transaction_color", type="string", description="日交易次数超出限制颜色"),
 *   @OA\Property(property="image", type="string", description="图片路径"),
 *   @OA\Property(property="status", type="string", description="状态"),
 *   @OA\Property(property="bank", ref="#/components/schemas/Bank"),
 *   @OA\Property(property="paymentGroup", ref="#/components/schemas/PaymentGroup"),
 *   @OA\Property(property="images", description="图片", ref="#/components/schemas/Image"),
 * )
 */
class CompanyBankAccountTransformer extends Transformer
{
    protected $availableIncludes = ['paymentGroup', 'images', 'bank'];

    public function transform(CompanyBankAccount $account)
    {
        // 兼容前端调用机制
        $inputData = $this->data;
        if (isset($inputData['payment_group'])){
            $paymentGroups = $inputData['payment_group'];
        }else{
            $paymentGroups    = $this->data;
        }

        $accountCode      = $account->code;
        $paymentGroupList = array_filter($paymentGroups, function ($group) use ($accountCode) {
            if (is_array($group['account_code']) && !empty($group['account_code']) && in_array($accountCode, $group['account_code'])) {
                return true;
            }
            return false;
        });

        $paymentGroupNames = collect($paymentGroupList)->pluck('name')->toArray();


        $data = [
            'id'                       => $account->id,
            'platform_id'              => $account->platform_id,
            'type'                     => transfer_show_value($account->type, CompanyBankAccount::$types),
            'code'                     => $account->code,
            'currency'                 => $account->currency,
            'payment_group_id'         => $account->payment_group_id,
            'display_payment_group_id' => transfer_show_value($account->payment_group_id, PaymentGroup::getDropList()),
            'bank_id'                  => $account->bank_id,
            'bank_code'                => $account->bank_code,
            'province'                 => $account->province,
            'city'                     => $account->city,
            'branch'                   => $account->branch,
            'account_name'             => $account->account_name,
            'account_no'               => $account->account_no,
            'phone'                    => $account->phone,
            'phone_asset'              => $account->phone_asset,
            'app_related'              => transfer_show_value($account->app_related, CompanyBankAccount::$appRelates),
            'first_balance'            => thousands_number($account->first_balance),
            'safe_key_pass'            => $account->safe_key_pass,
            'otp'                      => $account->otp,
            'display_otp'              => transfer_show_value($account->otp, CompanyBankAccount::$otps),
            'balance'                  => thousands_number($account->balance),
            'balance_color'            => $account->isBalanceExceedLimit() ? '#F56C6C' : '',
            'min_balance'              => thousands_number($account->min_balance),
            'max_balance'              => thousands_number($account->max_balance),
            'daily_fund_out'           => thousands_number($account->daily_fund_out),
            'daily_fund_out_limit'     => thousands_number($account->daily_fund_out_limit),
            'daily_fund_out_color'     => $account->isDailyFundOutExceedLimit() ? '#F56C6C' : '',
            'daily_fund_in'            => thousands_number($account->daily_fund_in),
            'daily_fund_in_limit'      => thousands_number($account->daily_fund_in_limit),
            'fee'                      => thousands_number($account->fee),
            'daily_fund_in_color'      => $account->isDailyFundInExceedLimit() ? '#F56C6C' : '',
            'daily_transaction'        => $account->daily_transaction,
            'daily_transaction_limit'  => $account->daily_transaction_limit,
            'daily_transaction_color'  => $account->isDailyFundTransactionExceedLimit() ? '#F56C6C' : '',
            'image'                    => get_image_url($account->bank->image),
            'status'                   => transfer_show_value($account->status, CompanyBankAccount::$statuses),
            'payment_group_name_list'  => implode(',', $paymentGroupNames),
        ];

        switch ($this->type) {
            case 'index':
                if ('VND' == $data['currency']) {
                    $data['first_balance']        = thousands_number($account->first_balance, 3);
                    $data['balance']              = thousands_number($account->balance, 3);
                    $data['min_balance']          = thousands_number($account->min_balance, 3);
                    $data['max_balance']          = thousands_number($account->max_balance, 3);
                    $data['daily_fund_out']       = thousands_number($account->daily_fund_out, 3);
                    $data['daily_fund_out_limit'] = thousands_number($account->daily_fund_out_limit, 3);
                    $data['daily_fund_in']        = thousands_number($account->daily_fund_in, 3);
                    $data['daily_fund_in_limit']  = thousands_number($account->daily_fund_in_limit, 3);
                }
                break;
            case 'show':
                if ('VND' == $data['currency']) {
                    $data['balance'] = thousands_number($account->balance, 3);
                }
                $data['user_name'] = $account->user_name;
                $data['password'] = $account->password;
                break;
            case 'front':
                $only         = ['id', 'platform_id', 'type', 'code', 'currency', 'bank_id', 'bank_code', 'account_name',
                    'account_no', 'image', 'status', 'branch'];
                $data         = collect($data)->only($only)->toArray();
                $bankNameList = Bank::getFrontDropList($account->currency);

                # 目前就泰国市场需要隐藏银行卡姓名和银行卡号
//                if (isset($this->data['currency']) && 'THB' == $this->data['currency']) {
//                    $data['account_name'] = str_repeat('*', mb_strlen($data['account_name']));
//                    $data['account_no']   = str_repeat('*', mb_strlen($data['account_no']));
//                }

                $data['bank_name'] = isset($bankNameList[$data['bank_id']]) ? $bankNameList[$data['bank_id']] : '';
                break;
            default:
                $data['user_name'] = $account->user_name;
                $data['password']  = $account->password;
                break;
        }

        return $data;
    }

    public function includePaymentGroup(CompanyBankAccount $companyBankAccount)
    {
        return $this->item($companyBankAccount->paymentGroup, new PaymentGroupTransformer());
    }

    public function includeImages(CompanyBankAccount $companyBankAccount)
    {
        return $this->collection($companyBankAccount->images, new ImageTransformer());
    }

    public function includeBank(CompanyBankAccount $companyBankAccount)
    {
        return $this->item($companyBankAccount->bank, new BankTransformer());
    }

}
