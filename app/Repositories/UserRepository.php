<?php
namespace App\Repositories;

use App\Models\Adjustment;
use App\Models\Config;
use App\Models\Currency;
use App\Models\GamePlatform;
use App\Models\PaymentGroup;
use App\Models\Reward;
use App\Models\RiskGroup;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserInfo;
use App\Models\Vip;
use App\Services\GamePlatformService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserRepository
{
    public static function find($id)
    {
        return User::query()->find($id);
    }

    /**
     * 根据名称获取会员
     *
     * @param  $name
     * @return User
     */
    public static function findByName($name)
    {
        return User::query()->isUser()->where('name', $name)->first();
    }

    public static function findAffiliateByName($name)
    {
        return User::query()->isAgent()->where('name', $name)->first();
    }

    public static function findAvailableReferralCode()
    {
        do {
            $code = str_random(8);
        } while (User::query()->where('referral_code', $code)->exists());

        return $code;
    }

    public static function findAvailableAffiliateCode()
    {
        do {
            $code = str_random(10);
        } while (User::query()->where('affiliate_code', $code)->exists());

        return $code;
    }

    public static function isPc($device)
    {
        return User::DEVICE_PC == $device;
    }

    public static function isMobile($device)
    {
        return User::DEVICE_MOBILE == $device;
    }

    public static function isAndroid($device)
    {
        return User::DEVICE_ANDROID == $device;
    }

    public static function isIos($device)
    {
        return User::DEVICE_IOS == $device;
    }


    /**
     * 转换audit中的值
     *
     * @param  string   $field  字段
     * @param  string   $value  值
     * @return string
     */
    public static function transformAudit($field, $value)
    {
        switch ($field) {
            case 'status':
                $result = User::$statuses[$value];
                break;
            case 'risk_group_id':
                $riskGroup = RiskGroup::findByCache($value);
                $result = $riskGroup ? $riskGroup->name : '';
                break;
            case 'payment_group_id':
                $paymentGroup = PaymentGroup::findByCache($value);
                $result = $paymentGroup ? $paymentGroup->name : '';
                break;
            case 'vip_id':
                $vip = Vip::findByCache($value);
                $result = $vip? $vip->name : '';
                break;
            case 'reward_id':
                $reward = Reward::findByCache($value);
                $result = $reward ? $reward->level : '';
                break;
            case 'odds':
                $odd = User::$odds;
                $result = isset($odd[$value]) ? $odd[$value] : null;
                break;
            case 'security_question':
                $result = isset(User::$securityQuestions[$value]) ? User::$securityQuestions[$value] : null;
                break;
            default:
                $result = $value ? $value : '';
        }

        return $result;
    }

    /**
     * 检查会员信息是否填写完整
     *
     * @param User $user
     * @return bool
     */
    public static function checkProfileVerified(User $user)
    {
        $userVerifyFields = [
            'security_question',
            'security_question_answer',
        ];

        $infoVerifyFields = [
            'gender'
        ];

        if (is_null($user->info->profile_verified_at)) {

            foreach ($infoVerifyFields as $field) {
                if (empty($user->info->$field)) {
                    return false;
                }
            }

            foreach ($userVerifyFields as $field) {
                if (empty($user->$field)) {
                    return false;
                }
            }

            # 验证完成更新验证时间
            $user->info->update([
                'profile_verified_at' => now()
            ]);
        }

        return true;
    }

    /**
     * 检查会员银行卡是否验证
     *
     * @param User $user
     * @return bool
     */
    public static function checkBankAccountVerified(User $user)
    {
        if (empty($user->info->bank_account_verified_at)) {

            # 获取币别
            $currencySet = Currency::findByCodeFromCache($user->currency);
            if ($user->report && $user->report->withdrawal >= $currencySet->bank_account_verify_amount) {
                # 银行卡验证完成更新验证时间
                $user->info->update([
                    'bank_account_verified_at' => now()
                ]);
            }

        }

        return true;
    }

    /**
     * 获取验证完成的个数
     *
     * @param UserInfo $info
     * @return mixed
     */
    public static function findVerifiedCount(UserInfo $info)
    {
        return collect($info)->only(['email_verified_at', 'phone_verified_at', 'profile_verified_at', 'bank_account_verified_at'])
                ->filter()
                ->count();
    }

    /**
     * 是否可以领取验证奖励
     *
     * @param UserInfo $info
     * @return bool
     */
    public static function isCanClaimVerifyPrize(UserInfo $info)
    {
        $count = static::findVerifiedCount($info);

        return 4 == $count && !$info->isClaimedVerifyPrize();
    }

    /**
     * 检查adjustment是否已经存在领取过的奖励
     *
     * @param User $user
     * @return bool
     */
    public static function checkClaimedVerifyPrize(User $user)
    {
        return Adjustment::query()->where('user_id', $user->id)
                ->where('category', Adjustment::CATEGORY_ACCOUNT_SAFETY)
                ->whereIn('status', Adjustment::$checkStatuses)
                ->exists();
    }

    /**
     * 添加登录失败次数
     *
     * @param User $user
     */
    public static function addLoginFailTimes(User $user)
    {
        $key = $user->name . '_' . 'login_fail_times';

        $value = Cache::rememberForever($key, function() {
            return 0;
        });

        # 检查错误次数是否达到指定次数
        $maxTimes = Config::findValue('max_login_fail_times',  5);
        if ($value + 1 >= $maxTimes) {
            # 锁定会员状态
            $user->updateStatus(User::STATUS_LOCKED);
            # 清空次数
            Cache::forever($key, 0);
        } else {
            Cache::increment($key);
        }
    }

    /**
     * 获取会员可用游戏平台code
     *
     * @param User $user
     * @return mixed
     */
    public static function getAvailableGamePlatformCode(User $user)
    {
        $platformCodes = GamePlatform::getEnablePlatformCode();

        return $user->gamePlatformUsers->whereIn('platform_code', $platformCodes)->where('balance_status', true)->pluck('platform_code')->toArray();
    }

    /**
     * 获取可用钱包列表
     *
     * @param  boolean  $isShowBalance  是否显示余额
     * @return mixed|array  [ platform_code => code+balance, ...]
     */
    public static function getActiveGamePlatformDropList(User $user, $isShowBalance=false)
    {
        $platformCodes = GamePlatform::getEnablePlatformCode();

        $platformUsers = $user->gamePlatformUsers->whereIn('platform_code', $platformCodes)->where('balance_status', true);

        $codes = [];
        $codes[UserAccount::MAIN_WALLET] = UserAccount::getLangName();
        if ($isShowBalance) {
            $codes[UserAccount::MAIN_WALLET] .= ' (' . format_number($user->account->getAvailableBalance(), 2) . ')';
        };
        foreach ($platformUsers as $platformUser) {
            $codes[$platformUser->platform_code] = $platformUser->platform->name;
            if ($isShowBalance) {
                $codes[$platformUser->platform_code] .= ' (' . format_number($platformUser->balance, 2) . ')';
            };
        }

        return $codes;
    }

    /**
     * 更新第三方odds
     *
     * @param User $user
     * @param $oldOdds
     * @param $newOdds
     */
    public static function updatePlatformUserOdds(User $user, $oldOdds, $newOdds)
    {
        if (!empty($newOdds) && $oldOdds != $newOdds) {
            try {
                (new GamePlatformService())->updatePlatformUserOdds($user);
            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }
        }
    }

    /**
     * 检查会员是否存在锁钱包的流水被要求
     *
     * @param $userId
     * @param $platformCode
     * @return bool
     */
    public static function checkNotCloseTurnoverExists($userId, $platformCode)
    {
        # 红利
        if (UserBonusPrizeRepository::checkNotClosePrizeExists($userId, $platformCode)) {
            return true;
        }

        # adjustment
        if (AdjustmentRepository::checkNotCloseExists($userId,  $platformCode)) {
            return true;
        }

        return false;
    }
}
