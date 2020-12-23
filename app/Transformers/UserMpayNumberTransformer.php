<?php
namespace App\Transformers;

use App\Models\UserMpayNumber;

/**
 * @OA\Schema(
 *   schema="UserMpayNumber",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="ID"),
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="area_code", type="string",description="区码"),
 *   @OA\Property(property="currency", type="string",description="币别"),
 *   @OA\Property(property="number", type="string",description="号码"),
 *   @OA\Property(property="status", type="integer",description="状态"),
 *   @OA\Property(property="display_status", type="string",description="状态显示"),
 *   @OA\Property(property="last_used_at", type="string",description="最近使用时间", format="date-time"),
 *   @OA\Property(property="created_at", type="string",description="创建时间", format="date-time"),
 *   @OA\Property(property="updated_at", type="string",description="更新时间", format="date-time"),
 *   @OA\Property(property="bank", ref="#/components/schemas/Bank"),
 * )
 */
class UserMpayNumberTransformer extends Transformer
{
    public function transform(UserMpayNumber $userMpayNumber)
    {
        return [
            'id'                => $userMpayNumber->id,
            'user_id'           => $userMpayNumber->user_id,
            'user_name'         => $userMpayNumber->user->name,
            'currency'          => $userMpayNumber->user->currency,
            'area_code'         => $userMpayNumber->area_code,
            'number'            => $userMpayNumber->number,
            'status'            => $userMpayNumber->status,
            'display_status'    => transfer_show_value($userMpayNumber->status, UserMpayNumber::$statuses),
            'last_used_at'      => convert_time($userMpayNumber->last_used_at),
            'created_at'        => convert_time($userMpayNumber->created_at),
            'updated_at'        => convert_time($userMpayNumber->updated_at),
        ];
    }
}