<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Remark extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'type', 'category','sub_category', 'reason', 'remove_reason', 'admin_name'
    ];

    protected $dates = [
        'deleted_at',
    ];

    # type
    const TYPE_HOLD_WITHDRAWAL              = 1;
    const TYPE_HOLD_DEPOSIT                 = 2;
    const TYPE_HOLD_WITHDRAWAL_AND_DEPOSIT  = 3;

    public static $types = [
        self::TYPE_HOLD_WITHDRAWAL              =>  'Hold Withdrawal',
        self::TYPE_HOLD_DEPOSIT                 =>  'Hold Deposit',
        self::TYPE_HOLD_WITHDRAWAL_AND_DEPOSIT  =>  'Hold Deposit and Withdrawal',
    ];

    public static $holdWithdrawalTypes = [
        self::TYPE_HOLD_WITHDRAWAL,
        self::TYPE_HOLD_WITHDRAWAL_AND_DEPOSIT,
    ];

    public static $holdDepositTypes = [
        self::TYPE_HOLD_DEPOSIT,
        self::TYPE_HOLD_WITHDRAWAL_AND_DEPOSIT,
    ];

    # category
    const CATEGORY_DEPOSIT          = 1;
    const CATEGORY_WITHDRAWAL       = 2;
    const CATEGORY_PROMOTION        = 3;
    const CATEGORY_BONUS_HUNTER     = 4;
    const CATEGORY_ADVANCE_CREDIT   = 5;
    const CATEGORY_BLACKLISTED      = 6;
    const CATEGORY_ADJUSTMENT       = 7;

    public static $categories = [
        self::CATEGORY_DEPOSIT          => 'Deposit',
        self::CATEGORY_WITHDRAWAL       => 'Withdrawal',
        self::CATEGORY_PROMOTION        => 'Promotion',
        self::CATEGORY_BONUS_HUNTER     => 'Bonus Hunter',
        self::CATEGORY_ADVANCE_CREDIT   => 'Advance Credit',
        self::CATEGORY_BLACKLISTED      => 'Blacklisted',
        self::CATEGORY_ADJUSTMENT       => 'Adjustment',
    ];

    # sub category
    const SUB_CATEGORY_DEPOSIT_OVER_CREDIT          = 1;
    const SUB_CATEGORY_DEPOSIT_UNDER_CREDIT         = 2;
    const SUB_CATEGORY_DEPOSIT_WRONGLY_CREDIT       = 3;
    const SUB_CATEGORY_DEPOSIT_DOUBLE_CREDIT        = 4;
    const SUB_CATEGORY_WITHDRAWAL_OVER_PAYOUT       = 5;
    const SUB_CATEGORY_WITHDRAWAL_UNDER_PAYOUT      = 6;
    const SUB_CATEGORY_WITHDRAWAL_WRONGLY_PAYOUT    = 7;
    const SUB_CATEGORY_WITHDRAWAL_DOUBLE_PAYOUT     = 8;

    public static $subCategories = [
        self::SUB_CATEGORY_DEPOSIT_OVER_CREDIT        => 'Over Credit',
        self::SUB_CATEGORY_DEPOSIT_UNDER_CREDIT       => 'Under Credit',
        self::SUB_CATEGORY_DEPOSIT_WRONGLY_CREDIT     => 'Wrongly Credit',
        self::SUB_CATEGORY_DEPOSIT_DOUBLE_CREDIT      => 'Double Credit',
        self::SUB_CATEGORY_WITHDRAWAL_OVER_PAYOUT     => 'Over Payout',
        self::SUB_CATEGORY_WITHDRAWAL_UNDER_PAYOUT    => 'Under Payout',
        self::SUB_CATEGORY_WITHDRAWAL_WRONGLY_PAYOUT  => 'Wrongly Payout',
        self::SUB_CATEGORY_WITHDRAWAL_DOUBLE_PAYOUT   => 'Double Payout',
    ];

    public static $subCategoriesRelated = [
        self::CATEGORY_DEPOSIT => [
            [
                "key" => self::SUB_CATEGORY_DEPOSIT_OVER_CREDIT,
                "value" => 'Over Credit'
            ],
            [
                "key" => self::SUB_CATEGORY_DEPOSIT_UNDER_CREDIT,
                "value" => 'Under Credit'
            ],
            [
                "key" => self::SUB_CATEGORY_DEPOSIT_WRONGLY_CREDIT,
                "value" => 'Wrongly Credit'
            ],
            [
                "key" => self::SUB_CATEGORY_DEPOSIT_DOUBLE_CREDIT,
                "value" => 'Double Credit'
            ],
        ],
        self::CATEGORY_WITHDRAWAL => [
            [
                "key" => self::SUB_CATEGORY_WITHDRAWAL_OVER_PAYOUT,
                "value" => 'Over Payout'
            ],
            [
                "key" => self::SUB_CATEGORY_WITHDRAWAL_UNDER_PAYOUT,
                "value" => 'Under Payout'
            ],
            [
                "key" => self::SUB_CATEGORY_WITHDRAWAL_WRONGLY_PAYOUT,
                "value" => 'Wrongly Payout'
            ],
            [
                "key" => self::SUB_CATEGORY_WITHDRAWAL_DOUBLE_PAYOUT,
                "value" => 'Double Payout'
            ],
        ]
    ];

    # 模型关联 start
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    # 模型关联 end

    # 查询本地作用域 start
    public function scopeType($query, $type)
    {
        if (static::TYPE_HOLD_WITHDRAWAL_AND_DEPOSIT == $type) {
            return $query->whereIn('type', [static::TYPE_HOLD_WITHDRAWAL, static::TYPE_HOLD_DEPOSIT]);
        } else {
            return $query->where('type', $type);
        }
    }

    public function scopeStartAt($query, $value)
    {
        return $query->where('created_at', '>=', $value);
    }

    public function scopeEndAt($query, $value)
    {
        return $query->where('created_at', '<=', $value);
    }

    public function scopeUserName($query, $userName)
    {
        return $query->whereHas('user', function($query) use ($userName) {
            $query->where('name', $userName);
        });
    }
    # 查询作用域 end

    # 方法 start
    public function remove($removeReason, $adminName)
    {
        $this->setPrimaryKeyQuery()->whereNull('deleted_at')->update([
            'deleted_at'        => now(),
            'remove_reason'     => $removeReason,
            'remove_admin_name' => $adminName,
        ]);
    }

    # 方法 end
}
