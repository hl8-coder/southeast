<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class CrmBoAdmin extends Authenticatable implements JWTSubject,Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'admin_name', 'admin_id', 'status', 'start_worked_at', 'end_worked_at', 'on_duty', 'tag_admin_id', 'tag_admin_name'
    ];
    public $casts = [
        'status'  => 'boolean',
        'on_duty' => 'boolean',
    ];

    protected $auditableEvents = ['created', 'updated', 'deleted',];

    protected $auditInclude = [
        'admin_name', 'admin_id', 'status', 'start_worked_at', 'end_worked_at', 'on_duty'
    ];

    public function getAuditFields()
    {
        return $this->auditInclude;
    }

    public function getAuditEvents():array
    {
        return $this->auditableEvents;
    }

    # status
    const STATUS_ACTIVE     = 1;
    const STATUS_INACTIVE   = 0;

    public static $statuses = [
        self::STATUS_ACTIVE     => 'active',
        self::STATUS_INACTIVE   => 'inactive',
    ];

    # on_duty
    const ON_DUTY_NO  = 0;
    const ON_DUTY_YES = 1;
    public static $onDuty = [
        self::ON_DUTY_NO  => 'duty off',
        self::ON_DUTY_YES => 'duty on',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}
