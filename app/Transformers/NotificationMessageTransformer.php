<?php

namespace App\Transformers;

use App\Models\DatabaseNotification;
use App\Models\NotificationMessage;

/**
 * @OA\Schema(
 *   schema="NotificationMessage",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="信息ID"),
 *   @OA\Property(property="sent_admin_name", type="string", description="发送管理员名称"),
 *   @OA\Property(property="category", type="integer", description="分类"),
 *   @OA\Property(property="display_category", type="string", description="分类显示"),
 *   @OA\Property(property="successNum", type="int", description="发送成功数量"),
 *   @OA\Property(property="failureNum", type="int", description="发送失败数量"),
 *   @OA\Property(property="totalNum", type="int", description="发送总人数"),
 *   @OA\Property(property="content", type="string", description="内容"),
 *   @OA\Property(property="created_at", type="string", description="创建时间"),
 * )
 */
class NotificationMessageTransformer extends Transformer
{
    public function transform(NotificationMessage $notificationMessage)
    {
        $data = [
            'id'                => $notificationMessage->id,
            'sent_admin_name'   => $notificationMessage->sent_admin_name,
            'category'          => $notificationMessage->category,
            'display_category'  => transfer_show_value($notificationMessage->category, DatabaseNotification::$categories),
            'message'           => $notificationMessage->message,
            'successNum'        => $notificationMessage->successNum,
            'failureNum'        => $notificationMessage->failureNum,
            'totalNum'          => $notificationMessage->totalNum,
            'created_at'        => convert_time($notificationMessage->created_at),
        ];

        switch ($this->type) {
            default:
                return $data;
            case 'index':
                return collect($data)->toArray();
        }
    }
}