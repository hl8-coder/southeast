<?php
namespace App\Transformers;

use App\Models\AffiliateCommission;
use App\Models\User;

/**
 * @OA\Schema(
 *   schema="AffiliateCommission",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="user_name", type="string", description="代理名称"),
 *   @OA\Property(property="full_name", type="string", description="真实姓名"),
 *   @OA\Property(property="affiliate_id", type="integer", description="代理id"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="bank_id", type="integer", description="银行id"),
 *   @OA\Property(property="province", type="string", description="省"),
 *   @OA\Property(property="city", type="string", description="市"),
 *   @OA\Property(property="branch", type="string", description="支行"),
 *   @OA\Property(property="account_no", type="string", description="开户账号"),
 *   @OA\Property(property="account_name", type="string", description="开户人姓名"),
 *   @OA\Property(property="address", type="string", description="地址"),
 *   @OA\Property(property="profit", type="number", description="总盈亏"),
 *   @OA\Property(property="stake", type="number", description="总投注"),
 *   @OA\Property(property="deposit", type="number", description="总充值"),
 *   @OA\Property(property="withdrawal", type="number", description="总提现"),
 *   @OA\Property(property="rebate", type="number", description="返点"),
 *   @OA\Property(property="promotion", type="number", description="优惠"),
 *   @OA\Property(property="rake", type="number", description="棋牌抽成金额"),
 *   @OA\Property(property="sub_adjustment", type="number", description="下级调整金额"),
 *   @OA\Property(property="affiliate_adjustment", type="number", description="代理调整金额"),
 *   @OA\Property(property="active_count", type="integer", description="活跃人数"),
 *   @OA\Property(property="transaction_cost", type="number", description="交易手续费"),
 *   @OA\Property(property="net_loss", type="number", description="盈亏基础数据"),
 *   @OA\Property(property="bear_cost", type="number", description="代理承担费用"),
 *   @OA\Property(property="product_cost", type="number", description="产品费用"),
 *   @OA\Property(property="parent_commission", type="number", description="上级抽成数据"),
 *   @OA\Property(property="sub_commission", type="number", description="下级抽成总金额"),
 *   @OA\Property(property="sub_commission_percent", type="number", description="下级抽成比例"),
 *   @OA\Property(property="previous_remain_commission", type="number", description="上期剩余分红金额"),
 *   @OA\Property(property="commission_percent", type="number", description="分红百分比"),
 *   @OA\Property(property="remain_commission", type="number", description="剩余分红金额"),
 *   @OA\Property(property="total_commission", type="number", description="总分红"),
 *   @OA\Property(property="payout_commission", type="number", description="支付分红"),
 *   @OA\Property(property="start_at", type="string", description="分红开始时间"),
 *   @OA\Property(property="end_at", type="string", description="分红结束时间"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="显示状态"),
 *   @OA\Property(property="last_access_at", type="string", description="最后访问时间"),
 *   @OA\Property(property="last_access_name", type="string", description="最后访问者"),
 *   @OA\Property(property="created_at", type="string", description="创建时间"),
 * )
 */
class AffiliateCommissionTransformer extends Transformer
{
    public function transform(AffiliateCommission $commission)
    {
        if (!empty($commission->calculate_setting)) {
            $commissionPercent = $commission->calculate_setting['value'];
        } else {
            $commissionPercent = !empty($commission->affiliate->commission_setting[0]) ? $commission->affiliate->commission_setting[0]['value'] : 0;
        }

        $result =  [
            'id'                    => $commission->id,
            'user_id'               => $commission->user_id,
            'user_name'             => $commission->user_name,
            'full_name'             => $commission->userInfo->full_name,
            'affiliate_id'          => $commission->affiliate_id,
            'currency'              => $commission->currency,
            # 银行卡相关
            'bank_id'               => $commission->bank_id,
            'bank_name'             => isset($commission->bank) ? $commission->bank->name:"-",
            'province'              => $commission->province,
            'city'                  => $commission->city,
            'branch'                => $commission->branch,
            'account_no'            => $commission->account_no,
            'account_name'          => $commission->account_name,
            'address'               => $commission->province . ' ' .$commission->city,

            'title'                         => !empty($commission->calculate_setting) ? $commission->calculate_setting['title'] : '',
            'profit'                        => thousands_number($commission->profit),
            'stake'                         => thousands_number($commission->stake),
            'deposit'                       => thousands_number($commission->deposit),
            'withdrawal'                    => thousands_number($commission->withdrawal),
            'rebate'                        => thousands_number($commission->rebate),
            'promotion'                     => thousands_number($commission->promotion),
            'rake'                          => thousands_number($commission->rake),
            'sub_adjustment'                => thousands_number($commission->sub_adjustment),
            'affiliate_adjustment'          => thousands_number($commission->affiliate_adjustment),
            'active_count'                  => $commission->active_count,
            'transaction_cost'              => thousands_number($commission->transaction_cost),
            'net_loss'                      => thousands_number($commission->net_loss),
            'bear_cost'                     => thousands_number($commission->bear_cost),
            'product_cost'                  => thousands_number($commission->product_cost),
            'expenses'                      => thousands_number($commission->product_cost + $commission->transaction_cost + $commission->bear_cost),
            'parent_commission'             => thousands_number($commission->parent_commission),
            'sub_commission'                => thousands_number($commission->sub_commission),
            'sub_commission_percent'        => $commission->sub_commission_percent,
            'previous_remain_commission'    => thousands_number($commission->previous_remain_commission),
            'commission_percent'            => $commissionPercent,
            'remain_commission'             => thousands_number($commission->remain_commission),
            'total_commission'              => thousands_number($commission->total_commission),
            'payout_commission'             => thousands_number($commission->payout_commission),
            'start_at'                      => convert_time($commission->start_at),
            'end_at'                        => convert_time($commission->end_at),
            'month'                         => $commission->start_at->format('Y-m'),
            'status'                        => $commission->status,
            'display_status'                => transfer_show_value($commission->status, AffiliateCommission::$statuses),
            'last_access_at'                => convert_time($commission->last_access_at),
            'last_access_name'              => $commission->last_access_name,
            'created_at'                    => convert_time($commission->created_at),
        ];

        switch ($this->type) {
            case 'pending':
                $totalMember = $this->data['total_member']->where('parent_id', $commission->user_id)->first();


                if ($totalMember) {
                    $result['total_member'] = $totalMember->total_member;
                } else {
                    $result['total_member'] = 0;
                }

                $newSignCount = $this->data['new_sign_count']->where('parent_id', $commission->user_id)
                                ->where('month', substr($commission->start_at->toDateString(), 0,7))
                                ->first();

                if ($newSignCount) {
                    $result['new_sign_count'] = $newSignCount->total_member;
                } else {
                    $result['new_sign_count'] = 0;
                }
                break;
        }

        return $result;
    }
}
