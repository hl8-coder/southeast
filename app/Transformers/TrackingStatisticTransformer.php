<?php


namespace App\Transformers;


use App\Models\TrackingStatistic;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TrackingStatisticTransformer extends Transformer
{
    protected $availableIncludes = ['user', 'userInfo'];

    /**
     * @OA\Schema(
     *   schema="TrackingStatistic",
     *   type="object",
     *   @OA\Property(property="id", type="integer", description="id"),
     *   @OA\Property(property="tracking_name", type="string", description="名称"),
     *   @OA\Property(property="date", type="string", description="当天第一次点击时间"),
     *   @OA\Property(property="total_click", type="integer", description="总点击次数"),
     *   @OA\Property(property="unique_click", type="integer", description="IP点击次数"),
     *   @OA\Property(property="url", type="integer", description="代理url"),
     * )
     */
    public function transform(TrackingStatistic $statistic)
    {
        $start = '';
        $end   = '';
        if (is_array($this->data) && $this->data != []) {
            $start = $this->data['start_at'];
            $end   = $this->data['end_at'];
        }
        $logs   = $statistic->trackingStatisticLogs;
        if (!empty($start)) {
            $logs = $logs->where('created_at', '>=', convert_time(Carbon::parse($start)->startOfDay()));
        }

        if (!empty($end)) {
            $logs = $logs->where('created_at', '<=', convert_time(Carbon::parse($end)->endOfDay()));
        }

        $totalClick   = $logs->count();
        $uniqueClick  = $logs->unique('ip')->count();
        $name = $statistic->tracking_name;
        if ($statistic->user->affiliate_code == $name) {
            $name = 'default';
        }
        $date = $logs->sortByDesc('created_at')->first();
        $data                  = [];
        $data['id']            = $statistic->id;
        $data['tracking_name'] = $name;
        $data['date']          = is_object($date) ? convert_time($date->created_at) : '';
        $data['total_click']   = $totalClick;
        $data['unique_click']  = $uniqueClick;
        switch ($this->type) {
            case 'backend_index':
                $count = $logs->where('url', '!=', '')->unique('url')->count();
                $data['url']  = $count;
                break;
        }
        return $data;
    }

    public function includeUser(TrackingStatistic $statistic)
    {
        return $this->item($statistic->user, new UserTransformer('affiliate_show'));
    }

    public function includeUserInfo(TrackingStatistic $statistic)
    {
        return $this->item($statistic->user->info, new UserInfoTransformer());
    }
}