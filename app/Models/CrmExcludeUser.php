<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;

class CrmExcludeUser extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public $fillable = ['user_id', 'user_name', 'affiliate_code', 'affiliated_code', 'is_affiliate',
        'action_admin_id', 'action_admin_name', 'review_at', 'review_by', 'status',];


    protected $auditEvents  = ['created', 'updated', 'deleted'];

    protected $auditInclude = ['user_id', 'user_name', 'affiliate_code', 'affiliated_code', 'is_affiliate', 'status'];

    public $casts = [
        'status' => 'boolean',
    ];

    const STATUS_TRUE = 1;
    const STATUS_FALSE = 0;
}
