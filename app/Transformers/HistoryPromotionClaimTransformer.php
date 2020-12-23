<?php

namespace App\Transformers;

use App\Models\PromotionClaimUser;

/**
 * @OA\Schema(
 *   schema="HistoryPromotionClaim",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="交易id"),
 *   @OA\Property(property="code", type="string", description="优惠代码"),
 *   @OA\Property(property="created_at", type="string", description="建立日期"),
 *   @OA\Property(property="status", type="string", description="状态"),
 *   @OA\Property(property="origin_status", type="integer", description="原始状态"),
 * )
 */
class HistoryPromotionClaimTransformer extends Transformer
{
    public function transform($data)
    {
        $languageKey = 'promotion.' . strtolower(transfer_show_value($data->status, PromotionClaimUser::$frontStatuses));
        $statusShow  = empty($data->promotion) ? '' : ($data->promotion->isNeedVerified() ? __($languageKey) : '');
        $originStatus  = empty($data->promotion) ? 0 : ($data->promotion->isNeedVerified() ? $data->status  : 0);
        return [
            'id'                => $data->id,
            'code'              => $data->related_code ?? $data->promotion_code,
            'created_at'        => convert_time($data->created_at),
            'status'            => $statusShow,
            'origin_status'     => $originStatus,
        ];
    }
}
