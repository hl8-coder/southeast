<?php

namespace App\Transformers;

use App\Models\Admin;
use App\Models\Model;
use App\Models\RiskGroup;

/**
 * @OA\Schema(
 *   schema="RiskGroup",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="分组id"),
 *   @OA\Property(property="name", type="string", description="名称"),
 *   @OA\Property(property="rules", type="array", description="名称", @OA\Items()),
 *   @OA\Property(property="description", type="string", description="描述"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 * )
 */
class RiskGroupTransformer extends Transformer
{
    public function transform(RiskGroup $group)
    {
        $last = $group->audits->sortByDesc('id')->first();
        if ($last && $last->user_type == 'App\Models\Admin') {
            $admin = Admin::query()->find($last->user_id);
        } else {
            $admin = null;
        }

        $rules        = $group->rules ?? [];
        $rulesForShow = collect(RiskGroup::$ruleLists)->only($rules)->toArray();
        return [
            'id'            => $group->id,
            'name'          => $group->name,
            'rules'         => $rules,
            'description'   => $group->description,
            'sort'          => $group->sort,
            'admin'         => $admin ? $admin->name : '',
            'status'        => transfer_show_value($group->status, Model::$booleanStatusesDropList),
            'rules_display' => $rulesForShow
        ];
    }
}
