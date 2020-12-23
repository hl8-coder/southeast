<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;
use OwenIt\Auditing\Contracts\Auditable;

class PaymentGroup extends Model implements Auditable
{
    use RememberCache, FlushCache;
    use \OwenIt\Auditing\Auditable;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    protected $rememberCacheTag = 'payment_groups';

    protected static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'name', 'remark', 'preset_risk_group_id', 'status', 'currency', 'account_code', 'last_save_admin', 'last_save_at'
    ];

    protected $casts = [
        'account_code' => 'array', // db:json
    ];


    protected $auditableEvents = ['updated',];

    protected $auditInclude = [
        'name', 'currency', 'account_code', 'remark', 'preset_risk_group_id', 'last_save_admin_id', 'last_save_admin_id'
    ];

    public function getAuditFields()
    {
        return $this->auditInclude;
    }

    public function getAuditEvents(): array
    {
        return $this->auditableEvents;
    }

    public static $statusList = [
        self::STATUS_ACTIVE   => 'active',
        self::STATUS_INACTIVE => 'inactive',
    ];

    public function accounts()
    {
        return $this->hasMany(CompanyBankAccount::class);
    }

    public function presetRiskGroup()
    {
        return $this->belongsTo(RiskGroup::class, 'preset_risk_group_id', 'id');
    }

    public static function getDropList()
    {
        return static::getAll()->pluck('name', 'id')->toArray();
    }
}
