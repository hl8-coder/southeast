<?php


namespace App\Transformers;


use App\Models\UserMessageDetail;

/**
 * @OA\Schema(
 *   schema="UserMessageDetail",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="用户信息ID"),
 *   @OA\Property(property="receive_user_name", type="string", description="接收会员名称"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="created_at", type="string", description="创建时间"),
 *   @OA\Property(property="status", type="boolean", description="状态"),
 * )
 */
class UserMessageDetailsTransformer extends Transformer
{
    public function transform(UserMessageDetail $userMessageDetail)
    {
        return [
            'id'                => $userMessageDetail->id,
            'receive_user_name' => $userMessageDetail->receive_user_name,
            'currency'          => $userMessageDetail->currency,
            'status'            => $userMessageDetail->getFriendlyStatus(),
            'created_at'        => convert_time($userMessageDetail->created_at),
        ];
    }
}