<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositLog extends Model
{
    # 类型
    const TYPE_ACCESS                         = 1;
    const TYPE_AMOUNT_DETAIL                  = 2;
    const TYPE_REMARKS                        = 3;
    const TYPE_MATCH                          = 4;
    const TYPE_UNMATCH                        = 5;
    const TYPE_HOLD                           = 6;
    const TYPE_RELEASE_HOLD                   = 7;
    const TYPE_APPROVE                        = 8;
    const TYPE_APPROVE_ADV                    = 9;
    const TYPE_APPROVE_PARTIAL                = 10;
    const TYPE_REVERT_ACTION                  = 11;
    const TYPE_APPROVE_PARTIAL_ADVANCE_CREDIT = 12;
    const TYPE_SUCCESS                        = 13;
    const TYPE_APPROVE_CHANGES                = 14;
    const TYPE_REJECT                         = 15;
    const TYPE_RECEIPT_UPLOAD                 = 16;
    const TYPE_RECEIPT_REMOVE                 = 17;
    const TYPE_CANCEL                         = 18;
    const TYPE_LOSE                           = 19;
    const TYPE_REQUEST_ADVANCE                = 20;
    const TYPE_CREATED                        = 21;  # 后台添加充值功能
    const TYPE_FINAL_APPROVE                  = 22;  # zpay, mpay, linepay部分上分使用

    public static $types = [
        self::TYPE_ACCESS                         => 'Access',
        self::TYPE_AMOUNT_DETAIL                  => 'Fund In Acoount / Amount',
        self::TYPE_REMARKS                        => 'Remarks',
        self::TYPE_MATCH                          => 'Match',
        self::TYPE_UNMATCH                        => 'Unmatch',
        self::TYPE_HOLD                           => 'Hold',
        self::TYPE_RELEASE_HOLD                   => 'Release Hold',
        self::TYPE_APPROVE                        => 'Approve',
        self::TYPE_APPROVE_ADV                    => 'Approve Adv',
        self::TYPE_APPROVE_PARTIAL                => 'Approve Partial',
        self::TYPE_REVERT_ACTION                  => 'Revert Action',
        self::TYPE_APPROVE_PARTIAL_ADVANCE_CREDIT => 'Approve Partial Advance Credit',
        self::TYPE_APPROVE_CHANGES                => 'Approve Changes',
        self::TYPE_SUCCESS                        => 'Success',
        self::TYPE_REJECT                         => 'Reject',
        self::TYPE_RECEIPT_UPLOAD                 => 'Receipt Upload',
        self::TYPE_RECEIPT_REMOVE                 => 'Receipt Remove',
        self::TYPE_CANCEL                         => 'Cancel',
        self::TYPE_LOSE                           => 'Lose',
        self::TYPE_REQUEST_ADVANCE                => 'Request Advance',
        # 后台添加充值功能
        self::TYPE_CREATED                        => 'Created',
        self::TYPE_FINAL_APPROVE                  => 'Final Approve',

    ];

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }

    public function scopeCreatedStartAt($query, $value)
    {
        return $query->whereHas('deposit', function ($query) use ($value) {
            return $query->where('created_at', '>=', $value);
        });
    }

    public function scopeCreatedEndAt($query, $value)
    {
        return $query->whereHas('deposit', function ($query) use ($value) {
            return $query->where('created_at', '<=', $value);
        });
    }

    public static function add(
        $adminName,
        $depositId,
        $type,
        $bankTransactionId = null,
        $reason = null
    )
    {
        $depositLog                      = new static();
        $depositLog->admin_name          = $adminName;
        $depositLog->deposit_id          = $depositId;
        $depositLog->type                = $type;
        $depositLog->bank_transaction_id = $bankTransactionId;
        $depositLog->reason              = $reason;

        $depositLog->save();

    }
}
