<?php
namespace App\Transformers;

use App\Models\NotificationReply;

/**
 * @OA\Schema(
 *   schema="NotificationReply",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="回复id"),
 *   @OA\Property(property="message", type="string", description="消息"),
 *   @OA\Property(property="is_admin", type="boolean", description="是否是客服回复"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class NotificationReplyTransformer extends Transformer
{
    public function transform(NotificationReply $reply)
    {
        return [
            'id'            => $reply->id,
            'message'       => $reply->message,
            'is_admin'      => !empty($reply->admin_name),
            'created_at'    => convert_time($reply->created_at),
        ];
    }
}