<?php


namespace App\Transformers;


use App\Models\CrmResource;

class CrmResourceTransformer extends Transformer
{
    /**
     * @OA\Schema(
     *   schema="CrmResource",
     *   type="object",
     *   @OA\Property(property="id", type="integer", description="crm resource id"),
     *   @OA\Property(property="full_name", type="string", description="客户全名"),
     *   @OA\Property(property="country_code", type="string", description="国家代号"),
     *   @OA\Property(property="phone", type="string", description="电话号码"),
     *   @OA\Property(property="admin_id", type="integer", description="电销人员ID"),
     *   @OA\Property(property="admin_name", type="string", description="电销人员名称"),
     *   @OA\Property(property="tag_admin_id", type="integer", description="分发者ID"),
     *   @OA\Property(property="tag_admin_name", type="string", description="分发者名称"),
     *   @OA\Property(property="tag_at", type="string", description="分发时间"),
     *   @OA\Property(property="is_auto", type="boolean", description="是否自动派发"),
     *   @OA\Property(property="status", type="boolean", description="订单状态"),
     *   @OA\Property(property="register", type="boolean", description="注册状态"),
     *   @OA\Property(property="call_status", type="boolean", description="号码呼叫状态"),
     *   @OA\Property(property="last_save_case_admin_id", type="string", description="最后修改者ID"),
     *   @OA\Property(property="last_save_case_admin_name", type="string", description="最后修改者"),
     *   @OA\Property(property="last_save_case_at", type="string", description="最后修改时间"),
     *   @OA\Property(property="updated_at", type="string", description="更新时间"),
     *   @OA\Property(property="created_at", type="string", description="创建时间"),
     *   @OA\Property(property="admin", description="管理员信息", ref="#/components/schemas/Admin"),
     * )
     */
    public function transform(CrmResource $crmResource)
    {
        return [
            'id'                        => $crmResource->id,
            'full_name'                 => $crmResource->full_name,
            'country_code'              => $crmResource->country_code,
            'phone'                     => $crmResource->phone,
            'admin_id'                  => $crmResource->admin_id,
            'admin_name'                => $crmResource->admin_name,
            'tag_admin_id'              => $crmResource->tag_admin_id,
            'tag_admin_name'            => $crmResource->tag_admin_name,
            'tag_at'                    => $crmResource->tag_at,
            'is_auto'                   => $crmResource->is_auto,
            'status'                    => transfer_show_value($crmResource->status, CrmResource::$status),
            'register'                  => $crmResource->register,
            'call_status'               => transfer_show_value($crmResource->call_status, CrmResource::$call_statuses),
            'last_save_case_admin_id'   => $crmResource->last_save_case_admin_id,
            'last_save_case_admin_name' => $crmResource->last_save_case_admin_name,
            'last_save_case_at'         => $crmResource->last_save_case_at,
            'updated_at'                => convert_time($crmResource->updated_at),
            'created_at'                => convert_time($crmResource->created_at),
        ];
    }
}

