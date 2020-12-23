<?php
namespace App\Transformers;

use App\Models\User;

/**
 * @OA\Schema(
 *   schema="AffiliateMemberReport",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="ID"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="name", type="string", description="会员名称"),
 *   @OA\Property(property="parent_id", type="integer", description="上级id"),
 *   @OA\Property(property="parent_id_list", type="string", description="上级id数组"),
 *   @OA\Property(property="parent_name", type="string", description="上级会员名称"),
 *   @OA\Property(property="parent_name_list", type="string", description="上级会员名称数组"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="状态显示"),
 *   @OA\Property(property="register_ip", type="string", description="注册ip"),
 *   @OA\Property(property="register_url", type="string", description="注册url"),
 *   @OA\Property(property="total_deposit", type="string", description="总充值金额"),
 *   @OA\Property(property="total_withdrawal", type="string", description="总提现金额"),
 *   @OA\Property(property="total_payment_fee", type="string", description="总手续费"),
 *   @OA\Property(property="total_bonus", type="string", description="总红利"),
 *   @OA\Property(property="total_rebate", type="string", description="总返点"),
 *   @OA\Property(property="created_at", type="string", format="date-time" ,description="创建时间"),
 *   @OA\Property(property="first_deposit_time", type="string", format="date-time" ,description="第一次充值时间"),
 * )
 */
class AffiliateMemberReportTransformer extends Transformer
{
    public function transform(User $user)
    {
        $start = array_key_exists('start', $this->data) ? $this->data['start'] : '';
        $end   = array_key_exists('end', $this->data) ? $this->data['end'] : '';
        $data  = [
            'id'               => $user->id,
            'name'             =>  hidden_name($user->name),
            'currency'         => $user->currency,
            'parent_id'        => $user->parent_id,
            'parent_id_list'   => $user->parent_id_list,
            'parent_name'      => $user->parent_name,
            'parent_name_list' => $user->parent_name_list,
            'created_at'       => convert_time($user->created_at),
            'register_url'     => $user->info->register_url,
            'register_ip'      => $user->info->register_ip,
            'status'           => $user->status,
            'display_status'   => transfer_show_value($user->status, User::$statuses),
        ];
        switch ($this->type) {
            case 'member_profileSummary':
                $firstDepositDate           = $user->deposits()
                    ->where(function ($query) use ($start, $end) {
                        if ($start) {
                            $query->where('created_at', '>=', $start);
                        }
                        if ($end) {
                            $query->where('created_at', '<=', $end);
                        }
                    })
                    ->orderBy('created_at', 'asc')
                    ->first();
                $time = '';
                if ($firstDepositDate) {
                    $time = convert_time($firstDepositDate->created_at);
                }
                $data['first_deposit_time'] = $time;
                break;
            case 'payment_report':
                $data['total_deposit']     = thousands_number($user->total_deposit);
                $data['total_withdrawal']  = thousands_number($user->total_withdrawal);
                $data['total_payment_fee'] = thousands_number($user->total_payment_fee);
                $data['total_bonus']       = thousands_number($user->total_bonus);
                $data['total_rebate']      = thousands_number($user->total_rebate);
                break;
        }
        return $data;
    }
}