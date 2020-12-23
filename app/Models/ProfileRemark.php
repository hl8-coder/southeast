<?php

namespace App\Models;

class ProfileRemark extends Model
{
    protected $fillable = [
        'category', 'remark', 'user_id', 'admin_name',
    ];

    const CATEGORY_CHANGE   = 1;
    const CATEGORY_ACCOUNT  = 2;
    const CATEGORY_PAYMENT  = 3;
    const CATEGORY_RISK     = 4;
    const CATEGORY_REWORD   = 5;
    const CATEGORY_OTHER    = 6;

    public static $categories = [
        self::CATEGORY_CHANGE   => 'Profile Change', // status email birthday full_name contact_no gender language
        self::CATEGORY_ACCOUNT  => 'Account Change', // password security_question odds
        self::CATEGORY_PAYMENT  => 'Payment Change', // payment_group
        self::CATEGORY_RISK     => 'Risk Change',  // risk
        self::CATEGORY_REWORD   => 'Vip and Reward Change', // member_profiling reward_level
        self::CATEGORY_OTHER    => 'Other Change',
    ];

    public static $categoriesForTranslation = [
        self::CATEGORY_CHANGE   => 'profile_change',
        self::CATEGORY_ACCOUNT  => 'account_change',
        self::CATEGORY_PAYMENT  => 'payment_change',
        self::CATEGORY_RISK     => 'risk_change',
        self::CATEGORY_REWORD   => 'vip_and_reward_change',
        self::CATEGORY_OTHER    => 'other_change',
    ];

    public static function add(
        $userId,
        $category,
        $remark,
        $adminName
    )
    {
        $profileRemark             = new static();
        $profileRemark->user_id    = $userId;
        $profileRemark->category   = $category;
        $profileRemark->remark     = $remark;
        $profileRemark->admin_name = $adminName;

        $profileRemark->save();

    }
}
