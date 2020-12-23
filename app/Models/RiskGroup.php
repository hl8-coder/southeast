<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;
use OwenIt\Auditing\Contracts\Auditable;

class RiskGroup extends Model implements Auditable
{
    use RememberCache, FlushCache, \OwenIt\Auditing\Auditable;

    protected $rememberCacheTag = 'risk_groups';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'name', 'rules', 'status', 'description', 'sort',
    ];

    protected $casts = [
        'rules' => 'array'
    ];

    const RULE_NO_ADJUSTMENT_REBATE        = 'no_adjustment_rebate';
    const RULE_NO_ADJUSTMENT_PROMOTION     = 'no_adjustment_promotion';
    const RULE_NO_ADJUSTMENT_RETENTION     = 'no_adjustment_retention';
    const RULE_NO_ADJUSTMENT_WELCOME_BONUS = 'no_adjustment_welcome_bonus';
    const RULE_NO_AUTO_REBATE              = 'no_auto_rebate';
    const RULE_NO_SHOW_PROMOTION_PAGE      = 'no_show_promotion_page';
    const RULE_USER_STATUS_INACTIVE        = 'user_status_inactive';
    const RULE_NO_ACCOUNT_SAFETY_BONUS     = 'no_account_safety_bonus';

    public static $ruleLists = [
        self::RULE_NO_ADJUSTMENT_REBATE        => 'No adjustment rebate',
        self::RULE_NO_ADJUSTMENT_PROMOTION     => 'No adjustment promotion',
        self::RULE_NO_ADJUSTMENT_RETENTION     => 'No adjustment retention',
        self::RULE_NO_ADJUSTMENT_WELCOME_BONUS => 'No adjustment welcome bonus',
        self::RULE_NO_AUTO_REBATE              => 'No auto rebate',
        self::RULE_NO_SHOW_PROMOTION_PAGE      => 'Hide FE promotion page',
        self::RULE_USER_STATUS_INACTIVE        => 'User account inactive',
        self::RULE_NO_ACCOUNT_SAFETY_BONUS     => 'No account safety bonus',
    ];

    protected $auditableEvents = ['created', 'updated', 'deleted',];

    protected $auditInclude = [
        'name', 'description', 'status', 'sort', 'rules'
    ];

    public function getAuditFields()
    {
        return $this->auditInclude;
    }

    public function getAuditEvents():array
    {
        return $this->auditableEvents;
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function getDropList()
    {
        return static::getAll()->pluck('name', 'id')->toArray();
    }
}
