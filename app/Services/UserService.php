<?php

namespace App\Services;

use App\Models\Adjustment;
use App\Models\Admin;
use App\Models\GamePlatform;
use App\Models\ProfileRemark;
use App\Models\RiskGroup;
use App\Models\TurnoverRequirement;
use App\Models\User;
use App\Models\Config;
use App\Models\Currency;
use App\Models\Affiliate;
use App\Models\UserInfo;
use App\Models\VerifiedPrizeBlackUser;
use App\Repositories\AdjustmentRepository;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use App\Models\UserLoginLog;
use Torann\GeoIP\Facades\GeoIP;
use App\Models\TrackingStatistic;
use App\Repositories\UserRepository;
use App\Repositories\GamePlatformUserRepository;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class UserService
{

    /**
     * 创建会员或会员
     *
     * @param   $data
     * @param string $registerIp 注册IP
     * @param string $registerUrl 注册链接
     * @return  User
     * @throws
     */
    public function store($data, $registerIp, $registerUrl = '')
    {
        $currency = $data['currency'];
        # 代理設定
        $isAgent = isset($data['is_agent']) ? $data['is_agent'] : false;
        $status  = $isAgent ? User::STATUS_LOCKED : User::STATUS_ACTIVE;

        $country = Currency::findByCodeFromCache($currency);

        # 检查小于18岁
        if (!empty($data['birth_at']) && !$this->check18NotRegister($data['birth_at'])) {
            throw new \Exception(__('user.UNDER_18_NOT_REGISTER'));
        }

        # 创建会员
        $user = new User([
            'name'     => $data['name'],
            'password' => bcrypt($data['password']),
            'currency' => $currency,
            'language' => $country->preset_language,
            'is_agent' => $isAgent,
            'status'   => $status,
        ]);

        # 各组别
        $user->vip_id        = Config::findValue('default_vip_id', null);
        $user->reward_id     = Config::findValue('default_reward_id', null);
        $user->risk_group_id = Config::findValue('default_risk_group_id', null);

        # 默认支付组会根据币别有所不同
        switch ($currency) {
            case 'THB':
                $paymentGroupKey = 'default_thb_payment_group_id';
                break;
            case 'VND':
                $paymentGroupKey = 'default_vnd_payment_group_id';
                break;
            default:
                $paymentGroupKey = 'default_payment_group_id';
                break;
        }
        $user->payment_group_id = Config::findValue($paymentGroupKey, null);

        # 被推荐码
        if (!empty($data['referrer_code'])) {
            $user->referrer_code = $data['referrer_code'];
        }

        # 推荐码
        $user->referral_code = UserRepository::findAvailableReferralCode();

        //上級代理資訊
        if (isset($data['affiliate_code'])) {

            if (!$parentAffiliate = Affiliate::where('code', $data['affiliate_code'])->first()) {
                throw new \Exception(__('user.WRONG_AFFILIATE'));
            }

            if (!$parentUser = User::where('id', $parentAffiliate->user_id)->first()) {
                throw new \Exception(__('user.WRONG_AFFILIATE'));
            }

            if ($user->currency == $parentUser->currency) {
                $user->parent_id        = $parentUser->id;
                $user->parent_name      = $parentUser->name;
                $user->parent_id_list   = $parentUser->id;
                $user->parent_name_list = $parentUser->name;
                $user->affiliated_code  = $parentUser->affiliate_code;
            }

            if ($parentUser->parent_id_list != null && $parentUser->parent_id_list != '') {
                $user->parent_id_list = "{$parentUser->parent_id_list},{$parentUser->id}";
            }

            if ($parentUser->parent_name_list != null && $parentUser->parent_name_list != '') {
                $user->parent_name_list = "{$parentUser->parent_name_list},{$parentUser->name}";
            }

        }

        $user->save();

        #创建会员信息
        $info = collect($data)
            ->only(['is_agent', 'country_code', 'phone', 'other_contact', 'full_name', 'email', 'birth_at', 'address', 'gender'])
            ->toArray();
        isset($info['phone']) and $info['phone'] = $this->removeFirstZero($info['phone']);
        $info['register_ip']  = $registerIp;
        $info['register_url'] = $registerUrl;
        $info                 = remove_null($info);
        $user->info()->create($info);

        # 建立会员账户
        $user->account()->create();

        # 建立风控
        $user->userRisks()->create();

        $affiliate = null;
        # 若为代理, 同步建立代理帐户
        if ($isAgent) {
            $user->status         = User::STATUS_PENDING;
            $user->affiliate_code = UserRepository::findAvailableAffiliateCode();
            $user->save();
            $affiliateCommissionLimit      = Config::findByCodeFromCache('affiliate_commission_limit');
            $affiliate                     = new Affiliate();
            $affiliate->user_id            = $user->id;
            $affiliate->code               = $user->affiliate_code;
            $affiliate->refer_by_code      = $user->affiliated_code;
            $commission_setting            = json_decode($affiliateCommissionLimit->value, true);
            $affiliate->commission_setting = $commission_setting;
            $affiliate->save();
            $track                = new TrackingStatistic();
            $track->tracking_name = $user->affiliate_code;
            $track->user_id       = $user->id;
            $track->user_name     = $user->name;
            $track->save();
        } else {
            # 建立所有会员第三方钱包, 且代理不需第三方钱包
            GamePlatformUserRepository::userRegisterAllPlatform($user);
        }

        return $user;
    }

    /**
     * 检查低于18岁不能注册
     *
     * @param $date
     * @return bool
     */
    public function check18NotRegister($date)
    {
        return now()->diffInYears($date) >= 18;
    }

    /**
     * 记录登录日志
     *
     * @param User $user
     * @param $ip
     * @param string $device
     * @param string $userAgent
     * @param string $remark
     */
    public function recordLoginLog(User $user, $ip, $device, $userAgent, $status, $remark = '')
    {
        # 获取地址信息
        $location = GeoIP::getLocation($ip);

        # 获取代理信息
        $agent = new Agent();
        $agent->setUserAgent($userAgent);

        $userLoginLog                = new UserLoginLog();
        $userLoginLog->user_id       = $user->id;
        $userLoginLog->user_name     = $user->name;
        $userLoginLog->ip            = $ip;
        $userLoginLog->device        = $device;
        $userLoginLog->equipment     = $agent->device();
        $userLoginLog->browser       = $agent->browser() . ' ' . $agent->version($agent->browser());
        $userLoginLog->country       = $location['country'] ?? '';
        $userLoginLog->city          = $location['city'] ?? '';
        $userLoginLog->state         = $location['state'] ?? '';
        $userLoginLog->success_login = $status;
        $userLoginLog->remark        = $remark;
        $userLoginLog->save();
    }

    public function resetPassword(User $user)
    {
        $password = str_random(8);

        $user->password = bcrypt($password);
        $user->save();
        $user->source_password = $password;

        return $user;
    }

    /**
     * 去除字符串首位的 0
     * @param string $string
     * @return string
     */
    public function removeFirstZero(string $string): string
    {
        if (substr($string, 0, 1) == '0') {
            return substr($string, 1);
        }
        return $string;
    }

    public function claimVerifyPrize(User $user, $platformCode, Admin $admin = null)
    {
        # 已领取
        if ($user->info->isClaimedVerifyPrize() || UserRepository::checkClaimedVerifyPrize($user)) {
            error_response(422, __('user.IS_ALREADY_CLAIMED_PRIZE'));
        }

        # 未完成验证
        if (!UserRepository::isCanClaimVerifyPrize($user->info)) {
            error_response(422, __('user.NOT_MEET_THE_CONDITIONS'));
        }

        # 风控组规则不允许领取
        $riskGroup = $user->riskGroup;
        if ($riskGroup){
            $rules = $riskGroup->rules ?? [];
            if (in_array(RiskGroup::RULE_NO_ACCOUNT_SAFETY_BONUS, $rules)){
                error_response(422, __('user.claim_account_safety_bonus_fail'));
            }
        }


        # 创建adjustment，并产生派发奖励
        try {
            $data = DB::transaction(function () use ($user, $platformCode, $admin) {

                $platform = GamePlatform::findByCode($platformCode);

                $currencySet = Currency::findByCodeFromCache($user->currency);

                # 创建adjustment
                $adjustment = Adjustment::query()->create([
                    'user_id'               => $user->id,
                    'user_name'             => $user->name,
                    'type'                  => Adjustment::TYPE_DEPOSIT,
                    'category'              => Adjustment::CATEGORY_ACCOUNT_SAFETY,
                    'amount'                => $currencySet->info_verify_prize_amount,
                    'turnover_closed_value' => $currencySet->info_verify_prize_amount,
                    'is_turnover_closed'    => false,
                    'platform_code'         => $platformCode,
                    'status'                => Adjustment::STATUS_PENDING,
                    'created_admin_name'    => !empty($admin) ? $admin->name : '',
                ]);

                $detail = (new GamePlatformService())->redirectTransfer($platform, $adjustment->user, $adjustment->amount, 'Adjustment', true);

                if ($detail->isSuccess()) {
                    # 统计数据
                    AdjustmentRepository::recordReport($adjustment);
                }

                return ['detail' => $detail, 'adjustment' => $adjustment];

            });
        } catch (\Exception $e) {
            error_response(422, $e->getMessage());
        }

        $detail     = $data['detail'];
        $adjustment = $data['adjustment'];

        if ($detail) {
            if ($detail->isFail()) {
                $adjustment->fail();
                error_response(422, __('user.claim_account_safety_bonus_fail'));
            } elseif ($detail->isSuccess()) {
                $adjustment->approve();
                # 更新已领取奖励
                $user->info->update(['claimed_verify_prize_at' => now()]);
                TurnoverRequirement::add($adjustment, $adjustment->is_turnover_closed);
            } else {
                $adjustment->waitingCheck();
                error_response(422, __('user.claim_account_safety_bonus_fail'));
            }
        }

        return $user->refresh();
    }

    public function setTokenInvalidate(User $user)
    {
        $apiGuard = auth('api');

        if (!empty($user->info->old_token)) {
            // 检查旧 Token 是否有效
            try {
                $apiGuard->setToken($user->info->old_token);

                if ($apiGuard->check()) {
                    // 加入黑名单
                    $apiGuard->invalidate();
                }
            } catch (\Exception $e) {
                return;
            }

        }
    }

    /**
     * 批量踢用户下线
     *
     * @param array $userIds
     *
     * @author  Martin
     * @date    2020/8/18 6:10 上午
     * @version viet-314
     */
    public function kickUsersOut(array $userIds)
    {
        $tokens = UserInfo::query()->whereIn('user_id', $userIds)->whereNotNull('old_token')->pluck('old_token')->toArray();
        if ($tokens){
            foreach ($tokens as $token){
                try {
                    $apiGuard = auth('api');
                    $apiGuard->setToken($token);

                    if ($apiGuard->check()) {
                        // 加入黑名单
                        $apiGuard->invalidate();
                    }
                } catch (\Exception $e) {
                    return;
                }
            }
        }
    }

    public function modifyStatusByRiskGroup(User $user, $riskGroupId, Admin $admin)
    {
        $riskGroup = RiskGroup::query()->find($riskGroupId);
        $rules = $riskGroup->rules;
        if (!empty($rules) && in_array(RiskGroup::RULE_USER_STATUS_INACTIVE, $rules)){
            $user->updateStatus(User::STATUS_INACTIVE);
            $remark = 'Admin modify user risk group cause user status change by risk group rules';
            ProfileRemark::add($user->id, ProfileRemark::CATEGORY_CHANGE, $remark, $admin->name);
        }
    }
}
