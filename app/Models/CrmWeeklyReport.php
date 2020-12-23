<?php

namespace App\Models;

class CrmWeeklyReport extends Model
{
    public $fillable = ['week', 'week_start_at', 'week_end_at', 'type', 'total_orders', 'total_type_orders',
        'successful', 'fail', 'voice_mail', 'hand_up', 'no_pick_up', 'invalid_number', 'not_own_number', 'call_back',
        'not_answer', 'not_interested_in', 'other', 'register', 'ftd_member', 'ftd_amount', 'adjustment_amount', 'admin_id',
        'admin_name', 'person_total_orders', 'person_total_type_orders', 'total_calls', 'total_type_calls',
        'person_total_calls', 'person_total_type_calls', 'success'];

    #纪录类型
    const TYPE_WELCOME          = 1;
    const TYPE_DAILY_RETENTION  = 2;
    const TYPE_RETENTION        = 3;
    const TYPE_NON_DEPOSIT      = 4;
    const TYPE_RESOURCE         = 5;
    public static $type = [
        self::TYPE_WELCOME         => 'Welcome',
        self::TYPE_DAILY_RETENTION => 'Daily Retention',
        self::TYPE_RETENTION       => 'Retention',
        self::TYPE_NON_DEPOSIT     => 'Non Deposit',
        self::TYPE_RESOURCE        => 'CRM Resource',
    ];

    /**
     * 获取统计报表中，某一周报表中所有 admin_id
     *
     * @param int $week
     * @return array
     *
     * @author  Martin
     * @date    22/7/2020 10:53 pm
     */
    public static function getWeeklyAdminIds(int $week)
    {
        return self::query()->select('admin_id')
            ->distinct()
            ->where('week', $week)
            ->pluck('admin_id')
            ->toArray();
    }
}
