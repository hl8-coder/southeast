<?php
namespace App\Transformers;

use OwenIt\Auditing\Models\Audit;

/**
 * @OA\Schema(
 *   schema="Audit",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="会员id"),
 *   @OA\Property(property="user_type", type="integer", description="用户类型(会员/管理员)"),
 *   @OA\Property(property="user_id", type="integer", description="用户id"),
 *   @OA\Property(property="old_value", type="string", description="旧值"),
 *   @OA\Property(property="new_value", type="string", description="新值"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class AuditTransformer extends Transformer
{
    public function transform(Audit $audit)
    {
        $data = [
            'id'            => $audit->id,
            'user_type'     => strtolower(str_replace('App\\Models\\', '', $audit->user_type)),
            'name'          => empty($audit->user) ? '' : $audit->user->name,
            'old_value'     => $audit->old_value,
            'new_value'     => $audit->new_value,
            'created_at'    => convert_time($audit->created_at),
        ];
        switch ($this->type){
            case 'crm_bo_admin':
                $data['action'] = $audit->event;
                break;
        }
        return $data;
    }
}
