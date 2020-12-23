<?php

namespace App\Models;

use Carbon\Carbon;

class UserProductDailyReport extends Report
{
    public function scopeStartAt($query, $value)
    {
        return $query->where('date', '>=', Carbon::parse($value)->toDateString());
    }

    public function scopeEndAt($query, $value)
    {
        return $query->where('date', '<=', Carbon::parse($value)->toDateString());
    }

    public static function getRebateReport(Rebate $rebate)
    {
        $date = now()->subDay()->toDateString();

        return static::query()->where('product_code', $rebate->product_code)
            ->where('date', $date)
            ->get();
    }

    public function scopeCurrency($query, $value)
    {
        return $query->whereHas('user', function ($query) use($value){
            return $query->where('currency', $value);
        });
    }
}
