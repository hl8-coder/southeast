<?php

namespace App\Models;


class VerifiedPrizeBlackUser extends Model
{
    public $fillable = ['user_id', 'user_name', 'add_by', 'add_by_admin_id', 'add_at'];

    public $casts = [
        'user_id'         => 'integer',
        'add_by_admin_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUserNameLike($query, $userName)
    {
        return $query->where('user_name', 'like', "%{$userName}%");
    }

    public function scopeAdminNameLike($query, $adminName)
    {
        return $query->where('add_by', 'like', "%{$adminName}%");
    }

    public function scopeUserId($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeAdminId($query, $adminId)
    {
        return $query->where('add_by_admin_id', $adminId);
    }

    public function scopeCurrency($query, $value)
    {
        return $query->whereHas('user', function ($query) use ($value) {
            return $query->where('currency', $value);
        });
    }
}
