<?php

namespace App\Transformers;

use App\Models\Config;
use App\Models\Model;

/**
 * @OA\Schema(
 *   schema="Config",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="code", type="string", description="辨识码"),
 *   @OA\Property(property="name", type="string", description="名称"),
 *   @OA\Property(property="group", type="string", description="分组"),
 *   @OA\Property(property="remarks", type="string", description="备注"),
 *   @OA\Property(property="is_front_show", type="string", description="是否前端显示"),
 *   @OA\Property(property="display_is_front_show", type="string", description="是否前端显示"),
 *   @OA\Property(property="type", type="string", description="值类型"),
 *   @OA\Property(property="value", type="string", description="值"),
 * )
 */
class ConfigTransformer extends Transformer
{
    public function transform(Config $config)
    {
        return [
            'id'                    => $config->id,
            'code'                  => $config->code,
            'name'                  => $config->name,
            'group'                 => transfer_show_value($config->group, Config::$groups),
            'remarks'               => $config->remarks,
            'is_front_show'         => $config->is_front_show,
            'display_is_front_show' => transfer_show_value($config->is_front_show, Model::$booleanDropList),
            'type'                  => $config->type,
            'value'                 => $config->value,
            'updated_at'            => convert_time($config->updated_at),
        ];
    }
}
