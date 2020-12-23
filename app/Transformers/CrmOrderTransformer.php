<?php

namespace App\Transformers;

use App\Models\Affiliate;
use App\Models\CrmOrder;
use App\Models\Deposit;
use App\Models\GameBetDetail;
use App\Models\User;
use Carbon\Carbon;

/**
 * @OA\Schema(
 *   schema="CrmOrder",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="CRM ID"),
 *   @OA\Property(property="user_id", type="integer", description="用户ID"),
 *   @OA\Property(property="affiliate_id", type="string", description="上级推荐代理ID"),
 *   @OA\Property(property="affiliate_code", type="integer", description="上级推荐代理code"),
 *   @OA\Property(property="type", type="integer", description="CRM Order类型"),
 *   @OA\Property(property="call_status", type="boolean", description="呼叫状态"),
 *   @OA\Property(property="status", type="boolean", description="crm order状态"),
 *   @OA\Property(property="is_auto", type="boolean", description="order是否为自动分配"),
 *   @OA\Property(property="tag_admin_id", type="string", description="标记管理员ID"),
 *   @OA\Property(property="tag_admin_name", type="string", description="标记管理员"),
 *   @OA\Property(property="tag_at", type="string", description="标记详细时间"),
 *   @OA\Property(property="admin_id", type="string", description="管理员id"),
 *   @OA\Property(property="admin_name", type="string", description="管理员"),
 *   @OA\Property(property="last_save_case_admin_id", type="integer", description="最后编辑者ID"),
 *   @OA\Property(property="last_save_case_admin_name", type="string", description="最后编辑者名称"),
 *   @OA\Property(property="last_save_case_at", type="string", description="最后编辑时间"),
 *   @OA\Property(property="updated_at", type="string", description="更新时间", format="date-time"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 *   @OA\Property(property="first_deposit_date", type="string", description="首次充值时间", format="date-time"),
 *   @OA\Property(property="first_deposit_amount", type="string", description="首次充值金额"),
 *   @OA\Property(property="last_deposit_date", type="string", description="最后充值时间", format="date-time"),
 *   @OA\Property(property="last_deposit_amount", type="string", description="最后充值金额"),
 *   @OA\Property(property="deposit", type="boolean", description="是否有充值"),
 *   @OA\Property(property="user", description="会员信息", ref="#/components/schemas/User"),
 * )
 */
class CrmOrderTransformer extends Transformer
{

    protected $defaultIncludes = ['user', 'userInfo'];

    public function transform(CrmOrder $crm_order)
    {
        $deposit     = $crm_order->user->depositsSuccessFirst->first();
        $lastDeposit = $crm_order->user->depositsSuccessLatest->first();
        return [
            'id'                        => $crm_order->id,
            'user_id'                   => $crm_order->user_id,
            'affiliate_id'              => empty($crm_order->affiliate_id) ? null : $crm_order->affiliate_id,
            'affiliated_code'           => $crm_order->affiliated_code,
            'type'                      => transfer_show_value($crm_order->type, CrmOrder::$type),
            'status'                    => transfer_show_value($crm_order->status, CrmOrder::$status),
            'call_status'               => transfer_show_value($crm_order->call_status, CrmOrder::$call_statuses),
            'is_auto'                   => transfer_show_value($crm_order->is_auto, CrmOrder::$booleanDropList),
            'tag_admin_id'              => $crm_order->tag_admin_id,
            'tag_admin_name'            => $crm_order->tag_admin_name, //派發者
            'tag_at'                    => convert_time($crm_order->tag_at) ? '' : $crm_order->tag_at,//派發時間
            'admin_id'                  => $crm_order->admin_id, //BO USER ID, 被指派
            'admin_name'                => $crm_order->admin_name, //BO USER NAME, 被指派
            'last_save_case_admin_id'   => $crm_order->last_save_case_admin_id,
            'last_save_case_admin_name' => $crm_order->last_save_case_admin_name,
            'last_save_case_at'         => convert_time($crm_order->last_save_case_at) ? '' : $crm_order->last_save_case_at,
            'updated_at'                => convert_time($crm_order->updated_at),
            'created_at'                => convert_time($crm_order->created_at),
            'first_deposit_date'        => empty($deposit) ? '' : $deposit->deposit_at,
            'first_deposit_amount'      => empty($deposit) ? '' : thousands_number($deposit->amount),
            'last_deposit_date'         => empty($lastDeposit) ? '' : $lastDeposit->deposit_at,
            'last_deposit_amount'       => empty($lastDeposit) ? '' : thousands_number($lastDeposit->amount),
            'deposit'                   => CrmOrder::$booleanDropList[!is_null($crm_order->user->first_deposit_at)],
        ];
    }

    public function includeUser(CrmOrder $crm_order)
    {
        return $this->item($crm_order->user, new UserTransformer());
    }

    public function includeUserInfo(CrmOrder $crm_order)
    {
        return $this->item($crm_order->userInfo, new UserInfoTransformer());
    }
}
