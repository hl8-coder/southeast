<?php

namespace App\Transformers;

use App\Models\Model;
use App\Models\PaymentGroup;
use App\Models\RiskGroup;

/**
 * @OA\Schema(
 *   schema="PaymentGroup",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="分组id"),
 *   @OA\Property(property="name", type="string", description="名称"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="account_code", type="array", description="账号代号",@OA\Items()),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="preset_risk_group_id", type="integer", description="预设风控组别id"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="last_save_admin", type="string", description="admin"),
 *   @OA\Property(property="last_save_at", type="date", description="最后操作时间"),
 * )
 */
class PaymentGroupTransformer extends Transformer
{
    protected $defaultIncludes = ['presetRiskGroup'];

    public function transform(PaymentGroup $group)
    {
        $data = [
            'id'                   => $group->id,
            'name'                 => $group->name,
            'currency'             => $group->currency,
            'account_code'         => empty($group->account_code) ? [] : $group->account_code,
            'remark'               => $group->remark,
            'preset_risk_group_id' => $group->preset_risk_group_id,
            'status'               => transfer_show_value($group->status, Model::$booleanStatusesDropList),
            'last_save_admin'      => $group->last_save_admin,
            'last_save_at'         => convert_time($group->last_save_at),
        ];
        if ($group->preset_risk_group == null){
            $data['presetRiskGroup'] = null;
        }
        return $data;
    }

    public function includePresetRiskGroup(PaymentGroup $paymentGroup)
    {
        if ($paymentGroup->presetRiskGroup == null){
            return null;
        }
        return $this->item($paymentGroup->presetRiskGroup, new RiskGroupTransformer());
    }
}
