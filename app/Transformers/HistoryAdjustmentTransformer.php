<?php

namespace App\Transformers;

use App\Models\Adjustment;
use App\Models\GamePlatformTransferDetail;

/**
 * @OA\Schema(
 *   schema="HistoryAdjustment",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="交易id"),
 *   @OA\Property(property="order_no", type="string", description="订单号"),
 *   @OA\Property(property="type", type="string", description="类型"),
 *   @OA\Property(property="category", type="string", description="分类"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="显示状态"),
 *   @OA\Property(property="amount", type="string", description="金额"),
 *   @OA\Property(property="created_at", type="string", description="建立日期")
 * )
 */
class HistoryAdjustmentTransformer extends Transformer
{
    public function transform($data)
    {
        switch ($data->status) {
            case Adjustment::STATUS_SUCCESSFUL:
                $status        = 1;
                $displayStatus = __('history.successful');
                break;
            case Adjustment::STATUS_PENDING:
                $status        = 2;
                $displayStatus = __('history.pending');
                break;
            default:
                $displayStatus = __('history.failed');
                $status        = 3;
                break;
        }

        return [
            'id'             => $data->id,
            'order_no'       => $data->order_no,
            'type'           => transfer_show_value($data->type, Adjustment::$frontTypes),
            'category'       => transfer_show_value($data->category, Adjustment::$categories),
            'status'         => $status,
            'display_status' => $displayStatus,
            'amount'         => thousands_number($data->amount),
            'created_at'     => convert_time($data->created_at),
        ];
    }
}
