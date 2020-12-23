<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Admin extends Authenticatable implements JWTSubject, Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name', 'nick_name', 'password', 'operate_password', 'description', 'language',
    ];


    protected $auditInclude = [
        'nick_name', 'password', 'operate_password', 'language',
    ];

    protected $casts = [
        'status'         => 'boolean',
        'is_super_admin' => 'boolean',
    ];

    protected $dates = [
        'deleted_at'
    ];

    # status
    const STATUS_ACTIVE     = 1;
    const STATUS_INACTIVE   = 0;

    public static $statuses = [
        self::STATUS_ACTIVE     => 'active',
        self::STATUS_INACTIVE   => 'inactive',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function roles()
    {
        return $this->belongsToMany(AdminRole::class)->withTimestamps();
    }

    public function generateTags() : array
    {
        $data = $this->getUpdatedEventAttributes();
        return array_keys($data[1]);
    }
}
