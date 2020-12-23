<?php


namespace App\Transformers;


use App\Models\UserRisk;

/**
 * @OA\Schema(
 *   schema="UserRisk",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="ID"),
 *   @OA\Property(property="user_id", type="integer", description="会员ID"),
 *   @OA\Property(property="behavior", type="integer", description="行为"),
 *   @OA\Property(property="display_behavior", type="string", description="行为"),
 *   @OA\Property(property="risk", type="integer", description="风险"),
 *   @OA\Property(property="display_risk", type="string", description="风险"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="updated_by", type="string", description="修改者"),
 *   @OA\Property(property="created_at", type="string", description="时间"),
 * )
 */
class UserRiskTransformer extends Transformer
{
    public function transform(UserRisk $risk)
    {
        return [
            'id'                => $risk->id,
            'user_id'           => $risk->user_id,
            'user_name'         => $risk->user->name,
            'behaviour'         => $risk->behaviour,
            'remark'            => $risk->remark,
            'risk'              => $risk->risk,
            'display_behaviour' => transfer_show_value($risk->behaviour, UserRisk::$behaviour),
            'display_risk'      => transfer_show_value($risk->risk, UserRisk::$risk),
            'updated_by'        => $risk->updated_by,
            'created_at'        => convert_time($risk->created_at),
        ];
    }
}