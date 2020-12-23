<?php

namespace App\Models;


class KpiReport extends Model
{
    protected $rememberCacheTag = 'kpi_reports';

    protected $fillable = [
        'date', 'currency', 'total_deposit', 'total_withdrawal', 'net_profit', 'total_new_members', 'total_active_members',
        'total_login_members', 'total_deposit_members', 'total_withdrawal_members', 'total_count_deposit',
        'total_count_withdrawal', 'total_turnover', 'total_payout', 'total_rebate', 'total_adjustment',
        'total_promotion_cost', 'total_promotion_cost_by_code', 'total_bank_fee'
    ];

    # 这里的常量值必须与上面的字段一一对得上，并且保持一致，后续该值传递的时候会间接去人需要更新的值
    const TYPE_TOTAL_DEPOSIT                = 'total_deposit';
    const TYPE_TOTAL_WITHDRAWAL             = 'total_withdrawal';
    const TYPE_NEW_MEMBERS                  = 'total_new_members';
    const TYPE_TOTAL_ACTIVE_MEMBERS         = 'total_active_members';
    const TYPE_TOTAL_LOGIN_MEMBERS          = 'total_login_members';
    const TYPE_TOTAL_DEPOSIT_MEMBERS        = 'total_deposit_members';
    const TYPE_TOTAL_WITHDRAWAL_MEMBERS     = 'total_withdrawal_members';
    const TYPE_TOTAL_COUNT_DEPOSIT          = 'total_count_deposit';
    const TYPE_TOTAL_COUNT_WITHDRAWAL       = 'total_count_withdrawal';
    const TYPE_TOTAL_TURNOVER               = 'total_turnover';
    const TYPE_TOTAL_PAYOUT                 = 'total_payout';
    const TYPE_TOTAL_REBATE                 = 'total_rebate';
    const TYPE_TOTAL_ADJUSTMENT             = 'total_adjustment';
    const TYPE_TOTAL_PROMOTION_COST         = 'total_promotion_cost';
    const TYPE_TOTAL_PROMOTION_COST_BY_CODE = 'total_promotion_cost_by_code';
    const TYPE_TOTAL_BANK_FEE               = 'total_bank_fee';
    const TYPE_NET_PROFIT                   = 'net_profit';

    public static $typeList = [
        self::TYPE_TOTAL_DEPOSIT                => 'total_deposit',
        self::TYPE_TOTAL_WITHDRAWAL             => 'total_withdrawal',
        self::TYPE_NEW_MEMBERS                  => 'total_new_members',
        self::TYPE_TOTAL_ACTIVE_MEMBERS         => 'total_active_members',
        self::TYPE_TOTAL_LOGIN_MEMBERS          => 'total_login_members',
        self::TYPE_TOTAL_DEPOSIT_MEMBERS        => 'total_deposit_members',
        self::TYPE_TOTAL_WITHDRAWAL_MEMBERS     => 'total_withdrawal_members',
        self::TYPE_TOTAL_COUNT_DEPOSIT          => 'total_count_deposit',
        self::TYPE_TOTAL_COUNT_WITHDRAWAL       => 'total_count_withdrawal',
        self::TYPE_TOTAL_TURNOVER               => 'total_turnover',
        self::TYPE_TOTAL_PAYOUT                 => 'total_payout',
        self::TYPE_TOTAL_REBATE                 => 'total_rebate',
        self::TYPE_TOTAL_ADJUSTMENT             => 'total_adjustment',
        self::TYPE_TOTAL_PROMOTION_COST         => 'total_promotion_cost',
        self::TYPE_TOTAL_PROMOTION_COST_BY_CODE => 'total_promotion_cost_by_code',
        self::TYPE_TOTAL_BANK_FEE               => 'total_bank_fee',
        self::TYPE_NET_PROFIT                   => 'net_profit',
    ];
}
