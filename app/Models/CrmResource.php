<?php

namespace App\Models;


class CrmResource extends Model
{
    public $fillable = ['full_name', 'country_code', 'phone', 'admin_id', 'admin_name', 'tag_admin_id', 'tag_admin_name',
        'is_auto', 'status', 'call_status', 'register', 'last_save_case_admin_id', 'last_save_case_admin_name', 'last_save_case_at', 'tag_at'];

    public $casts = [
        'status' => 'boolean'
    ];


    const STATUS_OPEN   = false;
    const STATUS_LOCKED = true;
    public static $status = [
        self::STATUS_LOCKED => 'locked',
        self::STATUS_OPEN   => 'open',
    ];


    const CALL_STATUS_SUCCESSFUL = 1;
    const CALL_STATUS_FAIL       = 0;
    public static $call_statuses = [
        self::CALL_STATUS_FAIL        => 'Fail',
        self::CALL_STATUS_SUCCESSFUL  => 'Successful',
    ];

    public function crmResourceCallLog()
    {
        return $this->hasMany(CrmResourceCallLog::class);
    }

    public function scopeTagStart($query, $date)
    {
        return $query->where('tag_at', '>=', $date);
    }

    public function scopeTagEnd($query, $date)
    {
        return $query->where('tag_at', '<=', $date);
    }

    public function scopeLastSaveStart($query, $date)
    {
        return $query->where('last_save_case_at', '>=', $date);
    }

    public function scopeLastSaveEnd($query, $date)
    {
        return $query->where('last_save_case_at', '<=', $date);
    }

    public function makeCall($callStatus)
    {
        $admin  = auth('admin')->user();
        $update = [
            'admin_id'                  => $admin->id,
            'admin_name'                => $admin->name,
            'last_save_case_admin_id'   => $admin->id,
            'last_save_case_admin_name' => $admin->name,
            'last_save_case_at'         => now(),
            'call_status'               => $callStatus,
        ];

        if ($this->tag_at == null){
            $update['tag_at'] = now();
        }
        return $this->update($update);
    }

}
