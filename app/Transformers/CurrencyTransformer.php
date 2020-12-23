<?php

namespace App\Transformers;

use App\Models\Currency;

/**
 * @OA\Schema(
 *   schema="Currency",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="ID"),
 *   @OA\Property(property="name", type="string", description="名称"),
 *   @OA\Property(property="code", type="string", description="代码"),
 *   @OA\Property(property="preset_language", type="string", description="预设语言"),
 *   @OA\Property(property="country", type="string", description="所属国家"),
 *   @OA\Property(property="country_code", type="string", description="国家电话编码"),
 *   @OA\Property(property="is_remove_three_zeros", type="boolean", description="是否去掉三个零"),
 *   @OA\Property(property="deposit_second_approve_amount", type="number", description="充值需要二次审核金额"),
 *   @OA\Property(property="withdrawal_second_approve_amount", type="number", description="提现需要二次审核金额"),
 *   @OA\Property(property="bank_account_verify_amount", type="number", description="个人银行卡验证金额"),
 *   @OA\Property(property="info_verify_prize_amount", type="number", description="资料验证完成奖金"),
 *   @OA\Property(property="max_deposit", type="number", description="最高充值限制"),
 *   @OA\Property(property="min_deposit", type="number", description="最低充值限制"),
 *   @OA\Property(property="max_withdrawal", type="number", description="最高出款限制"),
 *   @OA\Property(property="min_withdrawal", type="number", description="最低出款限制"),
 *   @OA\Property(property="max_daily_withdrawal", type="number", description="日出款总金额限制"),
 *   @OA\Property(property="min_transfer", type="number", description="最小转账限制"),
 *   @OA\Property(property="max_transfer", type="number", description="最大转账限制"),
 *   @OA\Property(property="commission", type="number", description="代理抽成百分比"),
 *   @OA\Property(property="payout_comm_mini_limit", type="string", description="代理盈亏最小出款金额"),
 *   @OA\Property(property="deposit_pending_limit", type="number", description="允许订单pending数量最大值"),
 *   @OA\Property(property="withdrawal_pending_limit", type="number", description="允许订单pending数量最大值"),
 *   @OA\Property(property="status", type="boolean", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="显示状态"),
 *   @OA\Property(property="created_at", type="string", description="创建时间"),
 *   @OA\Property(property="updated_at", type="string", description="更新时间"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 * )
 */
class CurrencyTransformer extends Transformer
{
    public function transform(Currency $currency)
    {
        $data = [
            'id'                               => $currency->id,
            'name'                             => $currency->name,
            'code'                             => $currency->code,
            'preset_language'                  => $currency->preset_language,
            'country'                          => $currency->country,
            'country_code'                     => $currency->country_code,
            'is_remove_three_zeros'            => $currency->is_remove_three_zeros,
            'deposit_second_approve_amount'    => thousands_number($currency->deposit_second_approve_amount),
            'withdrawal_second_approve_amount' => thousands_number($currency->withdrawal_second_approve_amount),
            'bank_account_verify_amount'       => thousands_number($currency->bank_account_verify_amount),
            'info_verify_prize_amount'         => thousands_number($currency->info_verify_prize_amount),
            'max_deposit'                      => thousands_number($currency->max_deposit),
            'min_deposit'                      => thousands_number($currency->min_deposit),
            'max_withdrawal'                   => thousands_number($currency->max_withdrawal),
            'min_withdrawal'                   => thousands_number($currency->min_withdrawal),
            'max_daily_withdrawal'             => thousands_number($currency->max_daily_withdrawal),
            'min_transfer'                     => thousands_number($currency->min_transfer),
            'max_transfer'                     => thousands_number($currency->max_transfer),
            'status'                           => $currency->status,
            'display_status'                   => Currency::$booleanStatusesDropList[$currency->status],
            'sort'                             => $currency->sort,
            'commission'                       => $currency->commission,
            'payout_comm_mini_limit'           => $currency->payout_comm_mini_limit,
            'deposit_pending_limit'            => $currency->deposit_pending_limit,
            'withdrawal_pending_limit'         => $currency->withdrawal_pending_limit,
            'created_at'                       => convert_time($currency->created_at),
            'updated_at'                       => convert_time($currency->updated_at),
        ];

        switch ($this->type) {
            case 'front_show':
                $name         = __('currency.' . strtoupper($data['code']));
                $data['name'] = empty($name) ? $data['code'] : $name;
                break;
        }
        return $data;
    }
}
