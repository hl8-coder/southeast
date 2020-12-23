<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification as Notification;

class DatabaseNotification extends Notification
{
    protected $dates = [
        'deleted_at',
    ];

    const Notification_CATEGORY_PROMOTION        = 1;
    const Notification_CATEGORY_BANKING          = 2;
    const Notification_CATEGORY_ACCOUNT_PROFIT   = 3;
    const Notification_CATEGORY_REBATE           = 4;
    const Notification_CATEGORY_BONUS            = 5;

    public static $categories = [
        self::Notification_CATEGORY_PROMOTION      => 'Promotion',
        self::Notification_CATEGORY_BANKING        => 'Banking',
        self::Notification_CATEGORY_ACCOUNT_PROFIT => 'Account Profit',
        self::Notification_CATEGORY_REBATE         => 'Rebate',
        self::Notification_CATEGORY_BONUS          => 'Welcome Bonus',
    ];
    # 模型关联 start
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'notifiable_id');
    }

    public function replies()
    {
        return $this->hasMany(NotificationReply::class, 'notification_id', 'id');
    }
    # 模型关联 end

    # 查询作用域 start
    public function scopeStartAt($query, $date)
    {
        return $query->where('created_at', '>=', Carbon::parse($date));
    }

    public function scopeEndAt($query, $date)
    {
        return $query->where('created_at', '<=', Carbon::parse($date));
    }

    public function scopeName($query, $name)
    {
        return $query->join('users', function($join) use ($name) {
            $join->on('users.id', '=', 'notifications.notifiable_id')->where('users.name', $name);
        });
    }

    public function scopeCurrency($query, $currency)
    {
        return $query->join('users', function($join) use ($currency) {
            $join->on('users.id', '=', 'notifications.notifiable_id')->where('users.currency', $currency);
        });
    }
    # 查询作用域 end

    # 方法 start
    /**
     * 读取单条记录
     */
    public function markSingleAsRead()
    {
        if (is_null($this->read_at)) {
            $this->update([
                'read_at' => now(),
            ]);
        }
    }
    # 方法 end
}
