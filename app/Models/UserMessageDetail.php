<?php

namespace App\Models;

use App\Jobs\SendMessageJob;
use Illuminate\Database\Eloquent\Model;

class UserMessageDetail extends Model
{
    protected $fillable = [
        'user_message_id', 'receive_user_id', 'receive_user_name',
        'phone', 'receive_user_status', 'currency', 'status', 'desc'
    ];

    const STATUS_DELIVERED  = 1;
    const STATUS_SENT       = 2;
    const STATUS_FAILED     = 3;

    public static $status = [
        self::STATUS_DELIVERED => 'delivered',
        self::STATUS_SENT      => 'sent',
        self::STATUS_FAILED    => 'failed',
    ];

    public function getFriendlyStatus()
    {
        $statusArray = self::$status;

        if (isset($statusArray[$this->status])) {
            return $statusArray[$this->status];
        }

        return $this->status;
    }

    public function isDelivered() {
        return $this->status == self::STATUS_DELIVERED;
    }

    public function addSendJob() {
        if($this->isDelivered()) {
            dispatch(new SendMessageJob($this))->onQueue('send_message');
        }
    }

    public function setToSent()
    {
        return $this->update([
            'status' => self::STATUS_SENT
        ]);
    }

    public function setToFailed($reason)
    {
        return $this->update([
            'status' => self::STATUS_FAILED,
            'desc'   => $reason
        ]);
    }

    public function userMessage()
    {
        return $this->belongsTo(UserMessage::class);
    }
}
