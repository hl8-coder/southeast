<?php

namespace App\Models;

class BatchTransferBackLog extends Model
{
    protected $guarded = [];

    const STATUS_TRANSFER_IN_SUCCESSFUL     = 1; # 转回主钱包成功
    const STATUS_TRANSFER_IN_FAIL           = 2; # 转回主钱包失败
    const STATUS_TRANSFER_OUT_SUCCESSFUL    = 3; # 转入到第三方钱包成功
    const STATUS_TRANSFER_OUT_FAIL          = 4; # 转入到第三方钱包失败

    public function platformUser()
    {
        return $this->belongsTo(GamePlatformUser::class, 'platform_user_id');
    }

    public static function add(
        $platformCode,
        $platformUserId,
        $userId,
        $userName,
        $amount,
        $status
    )
    {
        $log = new self();
        $log->platform_code     = $platformCode;
        $log->platform_user_id  = $platformUserId;
        $log->user_id           = $userId;
        $log->user_name         = $userName;
        $log->amount            = $amount;
        $log->status            = $status;
        $log->save();
        return $log;
    }
}
