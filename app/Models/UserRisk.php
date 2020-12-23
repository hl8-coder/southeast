<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRisk extends Model
{
    protected $fillable = [
        'user_id', 'behaviour', 'risk', 'remark', 'updated_by'
    ];

    # Behaviour
    const BEHAVIOR_NORMAL              = 1; # default
    const BEHAVIOR_SAME_IP             = 2;
    const BEHAVIOR_ALL_IN_BET          = 3;
    const BEHAVIOR_DOUBLE_BETTING      = 4;
    const BEHAVIOR_CONSISTENT_PLAY     = 5;
    const BEHAVIOR_OPPOSITE_BETTING    = 6;
    const BEHAVIOR_UNDERAGE            = 7;
    const BEHAVIOR_GROUP_BETTING       = 8;
    const BEHAVIOR_PROFESSIONAL_PLAYER = 9;
//    const BEHAVIOR_SOFTWARE_PLAYER          = 10;
    const BEHAVIOR_ABNORMAL_BETS              = 10;
    const BEHAVIOR_SAME_TABLE                 = 11;
    const BEHAVIOR_MULTIPLE_TABLES            = 12;
    const BEHAVIOR_LOWER_ODDS_BETS            = 13;
    const BEHAVIOR_FIXED_GAME                 = 14;
    const BEHAVIOR_UNDER_MONITORING           = 15;
    const BEHAVIOR_DUPLICATE_ACCOUNT          = 16;
    const BEHAVIOR_MULTIPLE_ACCOUNT           = 17;
    const BEHAVIOR_SELF_EXCLUSION             = 18;
    const BEHAVIOR_FINANCIAL_FRAUD            = 19;
    const BEHAVIOR_SUSPECTED_BONUS_HUNTER     = 20;
    const BEHAVIOR_CONFIRMED_BONUS_HUNTER     = 21;
    const BEHAVIOR_SUSPECTED_ROBOT_PLAYER     = 22;
    const BEHAVIOR_CONFIRMED_ROBOT_PLAYER     = 23;
    const BEHAVIOR_3RD_PARTY_DEPOSIT          = 24;
    const BEHAVIOR_HEDGING_PLAYER             = 25;
    const BEHAVIOR_SUSPECTED_ACCOUNT_TAKEOVER = 26;

    public static $behaviour = [
        self::BEHAVIOR_NORMAL                     => 'Normal',
        self::BEHAVIOR_SAME_IP                    => 'Same IP',
        self::BEHAVIOR_ALL_IN_BET                 => 'All-in Bet',
        self::BEHAVIOR_DOUBLE_BETTING             => 'Double Betting',
        self::BEHAVIOR_CONSISTENT_PLAY            => 'Consistent Play',
        self::BEHAVIOR_OPPOSITE_BETTING           => 'Opposite Betting',
        self::BEHAVIOR_UNDERAGE                   => 'Underage',
        self::BEHAVIOR_GROUP_BETTING              => 'Group Betting',
        self::BEHAVIOR_PROFESSIONAL_PLAYER        => 'Professional Player',
        //        self::BEHAVIOR_SOFTWARE_PLAYER     => 'Software Player',
        self::BEHAVIOR_ABNORMAL_BETS              => 'Abnormal bets',
        self::BEHAVIOR_SAME_TABLE                 => 'Same table',
        self::BEHAVIOR_MULTIPLE_TABLES            => 'Multiple tables',
        self::BEHAVIOR_LOWER_ODDS_BETS            => 'Lower Odds bets',
        self::BEHAVIOR_FIXED_GAME                 => 'Fixed game',
        self::BEHAVIOR_UNDER_MONITORING           => 'Under Monitoring',
        self::BEHAVIOR_DUPLICATE_ACCOUNT          => 'Duplicate Account',
        self::BEHAVIOR_MULTIPLE_ACCOUNT           => 'Multiple Account',
        self::BEHAVIOR_SELF_EXCLUSION             => 'Self Exclusion',
        self::BEHAVIOR_FINANCIAL_FRAUD            => 'Financial Fraud',
        self::BEHAVIOR_SUSPECTED_BONUS_HUNTER     => 'Suspected Bonus Hunter',
        self::BEHAVIOR_CONFIRMED_BONUS_HUNTER     => 'Confirmed Bonus Hunter',
        self::BEHAVIOR_SUSPECTED_ROBOT_PLAYER     => 'Suspected Robot Player',
        self::BEHAVIOR_CONFIRMED_ROBOT_PLAYER     => 'Confirmed Robot Player',
        self::BEHAVIOR_3RD_PARTY_DEPOSIT          => 'Third Party Deposit',
        self::BEHAVIOR_HEDGING_PLAYER             => 'Hedging Player',
        self::BEHAVIOR_SUSPECTED_ACCOUNT_TAKEOVER => 'Suspected Account Takeover',
    ];

    # Risk
    const RISK_NORMAL   = 1;
    const RISK_PROMO    = 2;
    const RISK_NETWORK  = 3;
    const RISK_WAGERING = 4;
    const RISK_PAYMENT  = 5;
    const RISK_RG       = 6;
    const RISK_PROFILE  = 7;

    public static $risk = [
        self::RISK_NORMAL   => 'Normal',
        self::RISK_PROMO    => 'Promo Risk',
        self::RISK_NETWORK  => 'Network Risk',
        self::RISK_WAGERING => 'Wagering Risk',
        self::RISK_PAYMENT  => 'Payment Risk',
        self::RISK_RG       => 'RG Risk',
        self::RISK_PROFILE  => 'Profile Risk',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
