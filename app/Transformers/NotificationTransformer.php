<?php
namespace App\Transformers;

use App\Models\DatabaseNotification;

/**
 * @OA\Schema(
 *   schema="Notification",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="通知id"),
 *   @OA\Property(property="user_id", type="string", description="user_id"),
 *   @OA\Property(property="message", type="string", description="内容"),
 *   @OA\Property(property="read_at", type="string", description="读取时间", format="date-time"),
 *   @OA\Property(property="admin_name", type="string", description="管理员名称"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 *   @OA\Property(property="user", description="会员", ref="#/components/schemas/User"),
 *   @OA\Property(property="replies", description="回复内容", ref="#/components/schemas/NotificationReply"),
 * )
 */
class NotificationTransformer extends Transformer
{
    protected $availableIncludes = ['user', 'replies'];

    public function transform(DatabaseNotification $notification)
    {
        $data = [
            'id'         => $notification->id,
            'user_id'    => $notification->notifiable_id,
            'category'   => $notification->notifiable_type,
            'message'    => $notification->data['message'],
            'read_at'    => convert_time($notification->read_at),
            'admin_name' => $notification->admin_name,
            'created_at' => convert_time($notification->created_at),
        ];

        switch ($this->type) {
            default:
                return $data;
            case 'front_index':
                return collect($data)->except(['category', 'admin_id'])->toArray();
                break;
        }
    }

    public function includeUser(DatabaseNotification $notification)
    {
        return $this->item($notification->user, new UserTransformer());
    }


    public function includeReplies(DatabaseNotification $notification)
    {
        return $this->collection($notification->replies->sortBy('created_at'), new NotificationReplyTransformer());
    }
}
