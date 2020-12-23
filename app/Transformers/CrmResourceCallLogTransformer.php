<?php


namespace App\Transformers;


use App\Models\CrmResourceCallLog;
use App\Models\CrmWeeklyReport;

class CrmResourceCallLogTransformer extends Transformer
{
    /**
     * @OA\Schema(
     *   schema="CrmResourceCallLog",
     *   type="object",
     *   @OA\Property(property="id", type="integer", description="crm resource call log id"),
     *   @OA\Property(property="crm_resource_id", type="integer", description="crm_resources id"),
     *   @OA\Property(property="admin_id", type="integer", description="admin id"),
     *   @OA\Property(property="channel", type="integer", description="通道"),
     *   @OA\Property(property="call_status", type="integer", description="呼叫状态"),
     *   @OA\Property(property="purpose", type="integer", description="呼叫目的"),
     *   @OA\Property(property="prefer_product", type="integer", description="偏爱的产品"),
     *   @OA\Property(property="prefer_bank", type="string", description="偏爱的银行"),
     *   @OA\Property(property="source", type="string", description="顾客来源"),
     *   @OA\Property(property="comment", type="string", description="备注"),
     *   @OA\Property(property="created_at", type="date", description="date"),
     *   @OA\Property(property="updated_at", type="date", description="date"),
     *   @OA\Property(property="admin", description="管理员信息", ref="#/components/schemas/Admin"),
     * )
     */
    public function transform(CrmResourceCallLog $crmResourceCallLog)
    {
        return [
            'id'              => $crmResourceCallLog->id,
            'crm_resource_id' => $crmResourceCallLog->crm_resource_id,
            'admin_id'        => $crmResourceCallLog->admin_id,
            'channel'         => transfer_show_value($crmResourceCallLog->channel, CrmResourceCallLog::$channel),
            'call_status'     => transfer_show_value($crmResourceCallLog->call_status, CrmResourceCallLog::$call_statuses),
            'comment'         => $crmResourceCallLog->comment,
            'purpose'         => transfer_show_value($crmResourceCallLog->purpose, CrmResourceCallLog::$purpose),
            'prefer_product'  => transfer_show_value($crmResourceCallLog->prefer_product, CrmResourceCallLog::$prefer_product),
            'prefer_bank'     => $crmResourceCallLog->prefer_bank,
            'source'          => transfer_show_value($crmResourceCallLog->source, CrmResourceCallLog::$source),
            'updated_at'      => convert_time($crmResourceCallLog->updated_at),
            'created_at'      => convert_time($crmResourceCallLog->created_at),
            'category'        => transfer_show_value(CrmWeeklyReport::TYPE_RESOURCE, CrmWeeklyReport::$type),
            'admin_name'      => $crmResourceCallLog->admin->name,
        ];
    }
}
