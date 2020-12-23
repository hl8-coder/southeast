<?php


namespace App\Transformers;


use App\Models\CrmExcludeUser;

/**
 * @OA\Schema(
 *   schema="CrmExcludeUser",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="ID"),
 *   @OA\Property(property="user_id", type="integer", description="user id"),
 *   @OA\Property(property="user_name", type="integer", description="user name"),
 *   @OA\Property(property="affiliate_code", type="integer", description="affiliate code"),
 *   @OA\Property(property="affiliated_code", type="integer", description="affiliated code"),
 *   @OA\Property(property="is_affiliate", type="integer", description="是否为代理"),
 *   @OA\Property(property="display_is_affiliate", type="integer", description="显示是否为代理"),
 *   @OA\Property(property="action_admin_id", type="integer", description="添加者管理员ID"),
 *   @OA\Property(property="action_admin_name", type="integer", description="添加着管理员名称"),
 *   @OA\Property(property="review_at", type="integer", description="审查时间"),
 *   @OA\Property(property="review_by", type="integer", description="审查者"),
 *   @OA\Property(property="status", type="integer", description="名单启用状态"),
 *   @OA\Property(property="display_status", type="integer", description="启用状态显示"),
 *   @OA\Property(property="created_at", type="date", description="date"),
 *   @OA\Property(property="updated_at", type="date", description="date"),
 * )
 */
class CrmExcludeUserTransformer extends Transformer
{
    public function transform(CrmExcludeUser $crmExcludeUser)
    {
        return [
            'id'                   => $crmExcludeUser->id,
            'user_id'              => $crmExcludeUser->user_id,
            'user_name'            => $crmExcludeUser->user_name,
            'affiliate_code'       => $crmExcludeUser->affiliate_code,
            'affiliated_code'      => $crmExcludeUser->affiliated_code,
            'is_affiliate'         => $crmExcludeUser->is_affiliate,
            'display_is_affiliate' => CrmExcludeUser::$booleanDropList[$crmExcludeUser->is_affiliate],
            'action_admin_id'      => $crmExcludeUser->action_admin_id,
            'action_admin_name'    => $crmExcludeUser->action_admin_name,
            'review_at'            => $crmExcludeUser->review_at,
            'review_by'            => $crmExcludeUser->review_by,
            'status'               => $crmExcludeUser->status,
            'display_status'       => CrmExcludeUser::$booleanStatusesDropList[$crmExcludeUser->status],
            'created_at'           => convert_time($crmExcludeUser->created_at),
            'updated_at'           => convert_time($crmExcludeUser->updated_at),
        ];
    }
}
