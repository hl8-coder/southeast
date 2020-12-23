<?php

namespace App\Transformers;

use App\Models\UserMessage;

/**
 * @OA\Schema(
 *   schema="UserMessage",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="用户信息ID"),
 *   @OA\Property(property="sent_admin_name", type="string", description="发送管理员名称"),
 *   @OA\Property(property="provider_code", type="string", description="供应商编码"),
 *   @OA\Property(property="category", type="int", description="分类"),
 *   @OA\Property(property="content", type="string", description="内容"),
 *   @OA\Property(property="created_at", type="string", description="创建时间"),
 * )
 */
class UserMessageTransformer extends Transformer
{
    public function transform(UserMessage $userMessage)
    {
        $data = [
            'id'              => $userMessage->id,
            'sent_admin_name' => $userMessage->sent_admin_name,
            'category'        => $userMessage->getFriendlyCategory(),
            'content'         => $userMessage->content,
            'number'          => $userMessage->number,
            'provider_code'   => $userMessage->provider_code,
            'created_at'      => convert_time($userMessage->created_at),
        ];

        switch ($this->type) {
            default:
                return $data;
            case 'index':
                return collect($data)->toArray();
        }
    }
}