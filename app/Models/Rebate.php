<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;
use Illuminate\Support\Facades\Log;

class Rebate extends Model
{
    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'rebates';

    protected static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'code', 'product_code', 'currencies', 'risk_group_id', 'vips', 'status', 'is_manual_send', 'admin_name',
    ];

    protected $dates = [
        'start_at', 'end_at',
    ];

    protected $casts = [
        'status'            => 'bool',
        'amount'            => 'float',
        'min_prize'         => 'float',
        'max_prize'         => 'float',
        'is_manual_send'    => 'bool',
        'currencies'        => 'array',
        'vips'              => 'array',
    ];

    # 属性修改器 start
    public function setCurrenciesAttribute($currencies)
    {
        $currencies = array_map(function($value) {
            $value['min_prize'] = (int)$value['min_prize'];
            $value['max_prize'] = (int)$value['max_prize'];
            return $value;
        }, $currencies);

        $this->attributes['currencies'] = json_encode($currencies);
    }

    public function setVipsAttribute($vips)
    {
        $vips = array_map(function($value) {
            $value['vip_id']     = (int)$value['vip_id'];
            $value['multipiler'] = (float)$value['multipiler'];
            return $value;
        }, $vips);

        $this->attributes['vips'] = json_encode($vips);
    }
    # 属性修改器 end

    public function isManualSend()
    {
        return $this->is_manual_send;
    }

    /**
     * 获取对应的币别设定
     *
     * @param $currency
     * @return mixed
     */
    public function getCurrencySet($currency)
    {
        return collect($this->currencies)->where('currency', $currency)->first();
    }

    /**
     * 获取对应的Vip设定
     *
     * @param $vipId
     * @return mixed
     */
    public function getVipSet($vipId)
    {
        return collect($this->vips)->where('vip_id', $vipId)->first();
    }
}
