<?php

namespace App\Transformers;

use App\Models\Model;
use App\Models\Remark;
use App\Models\User;

/**
 * @OA\Schema(
 *   schema="Remark",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="会员id"),
 *   @OA\Property(property="type", type="integer", description="类型"),
 *   @OA\Property(property="category", type="integer", description="分类"),
 *   @OA\Property(property="reason", type="string", description="理由"),
 *   @OA\Property(property="remove_reason", type="string", description="移除理由"),
 *   @OA\Property(property="admin_name", type="integer", description="创建管理员"),
 *   @OA\Property(property="remove_admin_name", type="integer", description="移除管理员"),
 *   @OA\Property(property="is_removed", type="boolean", description="是否移除"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", description="更新时间", format="date-time"),
 *   @OA\Property(property="deleted_at", type="string", description="移除时间"),
 *   @OA\Property(property="user", ref="#/components/schemas/User"),
 * )
 */
class RemarkTransformer extends Transformer
{
    protected $availableIncludes = ['user'];

    public function transform(Remark $remark)
    {
        $data =  [
            'id'                => $remark->id,
            'user_id'           => $remark->user_id,
            'type'              => transfer_show_value($remark->type, Remark::$types),
            'category'          => transfer_show_value($remark->category, Remark::$categories),
            'sub_category'      => $remark->sub_category ? transfer_show_value($remark->sub_category, Remark::$subCategories) : "",
            'reason'            => $remark->reason,
            'remove_reason'     => $remark->remove_reason,
            'admin_name'        => !empty($remark->admin_name) ? $remark->admin_name : 'System',
            'remove_admin_name' => $remark->remove_admin_name,
            'is_removed'        => !is_null($remark->deleted_at),
            'created_at'        => convert_time($remark->created_at),
            'updated_at'        => convert_time($remark->updated_at),
            'deleted_at'        => convert_time($remark->deleted_at),
        ];

        switch ($this->type) {
            case "batch_remark":
                $user = User::find($remark->user_id);
                $user_name = !empty($user) ? $user->name :"";
                $data['user_name'] = $user_name;
                break;
            default :
                break;
        }

        return $data;
    }

    public function includeUser(Remark $remark)
    {
        return $this->item($remark->user, new UserTransformer());
    }
}