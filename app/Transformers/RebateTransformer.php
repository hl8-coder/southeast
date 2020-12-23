<?php
namespace App\Transformers;

use App\Models\Model;
use App\Models\Rebate;
use App\Models\RiskGroup;
use App\Models\Vip;

/**
 * @OA\Schema(
 *   schema="Rebate",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="code", type="string", description="辨识码"),
 *   @OA\Property(property="product_code", type="string", description="产品code"),
 *   @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
 *      @OA\Property(property="currency", type="string", description="币别"),
 *      @OA\Property(property="min_prize", type="number", description="最小奖励值"),
 *      @OA\Property(property="max_prize", type="number", description="最大奖励值"),
 *   )),
 *   @OA\Property(property="risk_group_id", type="integer", description="风控组别id"),
 *   @OA\Property(property="vips", type="array", description="vip", @OA\Items(
 *      @OA\Property(property="vip_id", type="integer", description="vip ID"),
 *      @OA\Property(property="multipiler", type="number", description="奖励计算百分比"),
 *    )),
 *   @OA\Property(property="is_manual_send", type="boolean", description="是否需要手动派发奖励"),
 *   @OA\Property(property="status", type="boolean", description="状态"),
 *   @OA\Property(property="admin_name", type="string", description="管理员名称"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", description="更新时间", format="date-time"),
 * )
 */
class RebateTransformer extends Transformer
{
    public function transform(Rebate $rebate)
    {
        return [
            'id'                    => $rebate->id,
            'code'                  => $rebate->code,
            'product_code'          => $rebate->product_code,
            'currencies'            => $rebate->currencies,
            'risk_group_id'         => $rebate->risk_group_id,
            'vips'                  => $rebate->vips,
            'is_manual_send'        => $rebate->is_manual_send,
            'status'                => $rebate->status,
            'admin_name'            => $rebate->admin_name,
            'created_at'            => convert_time($rebate->created_at),
            'updated_at'            => convert_time($rebate->updated_at),
            'display_risk_group_id' => transfer_show_value($rebate->risk_group_id, RiskGroup::getDropList()),
            'display_is_manual_send'=> transfer_show_value($rebate->is_manual_send, Model::$booleanDropList),
            'display_status'        => transfer_show_value($rebate->status, Model::$booleanStatusesDropList),
        ];
    }
}