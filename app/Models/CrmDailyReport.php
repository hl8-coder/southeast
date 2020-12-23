<?php

namespace App\Models;


class CrmDailyReport extends Model
{
    public $fillable = ['week', 'date', 'type', 'total_orders', 'total_type_orders', 'person_total_orders', 'person_total_type_orders',
        'successful', 'fail', 'voice_mail', 'hand_up', 'no_pick_up', 'invalid_number', 'not_own_number', 'call_back',
        'not_answer', 'not_interested_in', 'other', 'admin_id', 'admin_name', 'success'];

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
}
