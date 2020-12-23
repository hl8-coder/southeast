<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationMessage extends Model
{
    protected $fillable = [
        'category', 'message', 'successNum', 'sent_admin_id',
        'sent_admin_name', 'totalNum', 'failureNum'
    ];

    public function notifications()
    {
        return $this->hasMany(DatabaseNotification::class);
    }

    # 作用域 start
    public function scopeStartAt($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    public function scopeUserName($query, $userName)
    {
        return $query->whereHas('notifications', function($query) use ($userName) {
            $query->join('users', function($join) use ($userName) {
                $join->on('users.id', '=', 'notifications.notifiable_id')->where('users.name', $userName);
            });
        });
    }

    public function scopeCurrency($query, $currency)
    {
        return $query->whereHas('notifications', function($query) use ($currency) {
            $query->join('users', function($join) use ($currency) {
                $join->on('users.id', '=', 'notifications.notifiable_id')->where('users.currency', $currency);
            });
        });
    }

    public function scopeEndAt($query, $date)
    {
        return $query->where('created_at', '<=', $date);
    }
    # 作用域 end
}
