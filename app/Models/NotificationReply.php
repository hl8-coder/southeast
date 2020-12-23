<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationReply extends Model
{
    protected $guarded = [];

    public function notification()
    {
        return $this->belongsTo(DatabaseNotification::class);
    }
}
