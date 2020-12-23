<?php

namespace App\Transformers;

use App\Models\CrmBoAdmin;
use App\Models\Model;

/**
 * @OA\Schema(
 *   schema="CrmBoAdmin",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="CRM BO ADMIN ID"),
 *   @OA\Property(property="admin_id", type="integer", description="管理者ID"),
 *   @OA\Property(property="admin_name", type="string", description="管理者账号"),
 *   @OA\Property(property="tag_admin_id", type="integer", description="排班者ID"),
 *   @OA\Property(property="tag_admin_name", type="string", description="排班者账号"),
 *   @OA\Property(property="status", type="boolean", description="是否在职"),
 *   @OA\Property(property="on_duty", type="boolean", description="是否上班"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date"),
 *   @OA\Property(property="updated_at", type="string", description="修改时间", format="date"),
 *   @OA\Property(property="start_worked_at", type="string", description="上班时间", format="date"),
 *   @OA\Property(property="end_worked_at", type="string", description="下班时间", format="date"),
 * )
 */
class CrmBoAdminTransformer extends Transformer
{

    public function transform(CrmBoAdmin $crmBoAdmin)
    {
        return [
            'id'              => $crmBoAdmin->id,
            'admin_id'        => $crmBoAdmin->admin_id,
            'admin_name'      => $crmBoAdmin->admin_name,
            'tag_admin_id'    => $crmBoAdmin->tag_admin_id,
            'tag_admin_name'  => $crmBoAdmin->tag_admin_name,
            'status'          => (int)$crmBoAdmin->status,
            'on_duty'         => (int)$crmBoAdmin->on_duty,
            'display_on_duty' => transfer_show_value($crmBoAdmin->on_duty, CrmBoAdmin::$onDuty),
            'display_status'  => transfer_show_value($crmBoAdmin->status, CrmBoAdmin::$statuses),
            'created_at'      => convert_time($crmBoAdmin->created_at),
            'updated_at'      => convert_time($crmBoAdmin->updated_at),
            'start_worked_at' => $crmBoAdmin->start_worked_at,
            'end_worked_at'   => $crmBoAdmin->end_worked_at,
        ];

    }
}
