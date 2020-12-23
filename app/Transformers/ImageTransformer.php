<?php

namespace App\Transformers;

use App\Models\Image;

/**
 * @OA\Schema(
 *   schema="Image",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="图片id"),
 *   @OA\Property(property="user_name", type="string", description="会员名称"),
 *   @OA\Property(property="url", type="string", description="地址"),
 *   @OA\Property(property="name", type="string", description="文件名称"),
 * )
 */
class ImageTransformer extends Transformer
{
    public function transform(Image $image)
    {
        return [
            'id'        => $image->id,
            'user_name' => empty($image->user) ? '' : $image->user->name,
            'url'       => get_image_url($image->path),
            'name'      => $image->name,
        ];
    }
}
