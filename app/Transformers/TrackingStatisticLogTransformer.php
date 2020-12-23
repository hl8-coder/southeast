<?php


namespace App\Transformers;


use App\Models\TrackingStatisticLog;

/**
 * @OA\Schema(
 *   schema="TrackingStatisticLog",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="ID"),
 *   @OA\Property(property="tracking_id", type="integer", description="tracking id"),
 *   @OA\Property(property="ip", type="string", description="ip"),
 *   @OA\Property(property="affiliate_code", type="string", description="代理号"),
 *   @OA\Property(property="url", type="string", description="来源地址"),
 *   @OA\Property(property="created_at", type="string",description="创建时间", format="date-time"),
 * )
 */
class TrackingStatisticLogTransformer extends Transformer
{
    protected $availableIncludes = ['trackingStatistic', 'trackingStatisticUser'];

    public function transform(TrackingStatisticLog $log)
    {
        $data = [];

        $data['id']             = $log->id;
        $data['tracking_id']    = $log->tracking_id;
        $data['ip']             = $log->ip;
        $data['affiliate_code'] = $log->affiliate_code;
        $data['type']           = $log->type;
        $data['url']            = $log->url;
        $data['created_at']     = convert_time($log->created_at);

        return $data;
    }

    public function includeTrackingStatistic(TrackingStatisticLog $log)
    {
        if ($log->trackingStatistic) {
            return $this->item($log->trackingStatistic, new TrackingStatisticTransformer());
        }
    }

    public function includeTrackingStatisticUser(TrackingStatisticLog $log)
    {
        if ($log->trackingStatistic) {
            if ($log->trackingStatistic->user)
                return $this->item($log->trackingStatistic->user, new UserTransformer('affiliate_show'));
        }
    }
}