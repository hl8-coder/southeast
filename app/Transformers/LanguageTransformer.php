<?php

namespace App\Transformers;

use App\Models\Language;

/**
 * @OA\Schema(
 *   schema="Language",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="name", type="string", description="名称"),
 *   @OA\Property(property="code", type="string", description="代码"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="updated_at", type="string", description="最后更新时间"),
 *   @OA\Property(property="display_status", type="string", description="显示状态"),
 * )
 */
class LanguageTransformer extends Transformer
{
    public function transform(Language $language)
    {
        return [
            'id'     => $language->id,
            'name'   => $language->name,
            'code'   => $language->code,
            'status' => $language->status,
            'updated_at' => convert_time($language->updated_at),
            'display_status' => Language::$booleanStatusesDropList[$language->status],
        ];
    }
}
