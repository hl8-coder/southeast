<?php

namespace App\Transformers;

use App\Models\CrmCallLog;
use App\Models\CrmOrder;

/**
 * @OA\Schema(
 *   schema="CrmCallLog",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="CALL LOG ID"),
 *   @OA\Property(property="crm_order_id", type="integer", description="crm order id"),
 *   @OA\Property(property="admin_id", type="integer", description="admin id"),
 *   @OA\Property(property="channel", type="integer", description="通道"),
 *   @OA\Property(property="category", type="string", description="crm订单类型"),
 *   @OA\Property(property="call_status", type="integer", description="呼叫状态"),
 *   @OA\Property(property="purpose", type="integer", description="呼叫目的"),
 *   @OA\Property(property="prefer_product", type="integer", description="偏爱的产品"),
 *   @OA\Property(property="prefer_bank", type="string", description="偏爱的银行"),
 *   @OA\Property(property="source", type="string", description="顾客来源"),
 *   @OA\Property(property="comment", type="string", description="备注"),
 *   @OA\Property(property="created_at", type="date", description="date"),
 *   @OA\Property(property="updated_at", type="date", description="date"),
 * )
 */
class CrmCallLogTransformer extends Transformer
{

    protected $availableIncludes = ['crmOrder'];
    public function transform(CrmCallLog $log)
    {
        return [
            'id'             => $log->id,
            'crm_order_id'   => $log->crm_order_id,
            'admin_id'       => $log->admin_id,
            'category'       => CrmOrder::$type[$log->crmOrder->type],
            'channel'        => CrmCallLog::$channel[$log->channel],
            'call_status'    => CrmCallLog::$call_statuses[$log->call_status],
            'purpose'        => empty($log->purpose) ? '' : CrmCallLog::$purpose[$log->purpose],
            'prefer_product' => empty($log->prefer_product) ? '' : CrmCallLog::$prefer_product[$log->prefer_product],
            'prefer_bank'    => $log->prefer_bank,
            'source'         => empty($log->source) ? '' : CrmCallLog::$source[$log->source],
            'comment'        => $log->comment,
            'created_at'     => convert_time($log->created_at),
            'updated_at'     => convert_time($log->updated_at),
        ];
    }

    public function includeCrmOrder(CrmCallLog $log)
    {
        $crmOrder = $log->crmOrder;
        return $this->item($crmOrder, new CrmOrderTransformer());
    }
}
