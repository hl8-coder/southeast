<?php


namespace App\Transformers;

use App\Models\ChangingConfig;


/**
 * @OA\Schema(
 *   schema="ChangingConfig",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="name", type="string", description="名称"),
 *   @OA\Property(property="code", type="string", description="代码"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="updated_at", type="string", description="最后更新时间"),
 *   @OA\Property(property="is_front_show", type="boolean", description="是否前端显示"),
 *   @OA\Property(property="display_is_front_show", type="string", description="是否前端显示"),
 *   @OA\Property(property="type", type="string", description="value类型"),
 *   @OA\Property(property="value", type="string", description="配置值"),
 * )
 */
class ChangingConfigTransformer extends Transformer
{
    public function transform(ChangingConfig $config)
    {
        $data = [
            'id'                    => $config->id,
            'name'                  => $config->name,
            'code'                  => $config->code,
            'remark'                => $config->remark,
            'is_front_show'         => $config->is_front_show,
            'display_is_front_show' => transfer_show_value($config->is_front_show, ChangingConfig::$booleanDropList),
            'type'                  => $config->type,
            'value'                 => $config->value,
            'updated_at'            => convert_time($config->updated_at),
        ];
        return $data;
    }
}
