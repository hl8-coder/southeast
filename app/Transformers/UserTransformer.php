<?php

namespace App\Transformers;

use App\Models\Currency;
use App\Models\Deposit;
use App\Models\PaymentGroup;
use App\Models\Reward;
use App\Models\RiskGroup;
use App\Models\User;
use App\Models\Vip;
use App\Models\Withdrawal;
use App\Models\UserLoginLog;

/**
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="ID"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="language", type="string", description="语言"),
 *   @OA\Property(property="display_language", type="string", description="语言显示"),
 *   @OA\Property(property="country", type="string", description="国家"),
 *   @OA\Property(property="name", type="string", description="会员名称"),
 *   @OA\Property(property="parent_id", type="integer", description="上级id"),
 *   @OA\Property(property="parent_id_list", type="string", description="上级id数组"),
 *   @OA\Property(property="parent_name", type="string", description="上级会员名称"),
 *   @OA\Property(property="parent_name_list", type="string", description="上级会员名称数组"),
 *   @OA\Property(property="notification_count", type="integer", description="未读消息数"),
 *   @OA\Property(property="vip_id", type="integer", description="vip id"),
 *   @OA\Property(property="display_vip_id", type="string", description="vip显示"),
 *   @OA\Property(property="reward_id", type="integer", description="积分等级id"),
 *   @OA\Property(property="display_reward_id", type="string", description="积分等级显示"),
 *   @OA\Property(property="risk_group_id", type="integer", description="风控组别id"),
 *   @OA\Property(property="display_risk_group_id", type="string", description="风控组别显示"),
 *   @OA\Property(property="payment_group_id", type="integer", description="支付组别id"),
 *   @OA\Property(property="display_payment_group_id", type="string", description="支付组别显示"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="状态显示"),
 *   @OA\Property(property="referral_code", type="string", description="推荐代码"),
 *   @OA\Property(property="referrer_code", type="string", description="推荐人代码"),
 *   @OA\Property(property="security_question", type="string", description="密保问题"),
 *   @OA\Property(property="odds", type="integer", description="赔率类型"),
 *   @OA\Property(property="is_need_change_password", type="boolean", description="是否需要强制修改密码"),
 *   @OA\Property(property="display_odds", type="integer", description="赔率类型显示"),
 *   @OA\Property(property="affiliate_id", type="string", description="代理id"),
 *   @OA\Property(property="created_at", type="string", format="date-time" ,description="创建时间"),
 *   @OA\Property(property="first_deposit_time", type="string", format="date-time" ,description="第一次充值时间"),
 *   @OA\Property(property="total_wallet_balance", type="string", format="date-time" ,description="钱包余额"),
 *   @OA\Property(property="total_deposit", type="string", description="总充值"),
 *   @OA\Property(property="total_withdrawal", type="string", description="总提现"),
 *   @OA\Property(property="affiliated_code", type="string", description="上级代理code"),
 *   @OA\Property(property="total_stake", type="string", description="总投注"),
 *   @OA\Property(property="total_profit", type="string", description="总盈亏"),
 *   @OA\Property(property="total_bet_num", type="string", description="总注数"),
 *   @OA\Property(property="total_effective_bet", type="string", description="总有效流水（暂时不用）"),
 *   @OA\Property(property="percent", type="string", description="盈亏/投注 百分比"),
 *   @OA\Property(property="per_bet", type="string", description="平均没注投额"),
 *   @OA\Property(property="info", ref="#/components/schemas/UserInfo"),
 *   @OA\Property(property="account", ref="#/components/schemas/UserAccount"),
 *   @OA\Property(property="gamePlatformUsers", type="array", @OA\Items(ref="#/components/schemas/GamePlatformUser")),
 *   @OA\Property(property="vip", ref="#/components/schemas/Vip"),
 *   @OA\Property(property="reward", ref="#/components/schemas/Reward"),
 *   @OA\Property(property="affiliate", ref="#/components/schemas/Affiliate"),
 *   @OA\Property(property="userRisks", ref="#/components/schemas/UserRisk"),
 * )
 */
class UserTransformer extends Transformer
{
    protected $availableIncludes = ['info', 'account', 'vip', 'reward', 'gamePlatformUsers', 'affiliate', 'userRisks', 'userLoginLogs'];

    public function transform(User $user)
    {
        $currencySet = Currency::findByCodeFromCache($user->currency);

        switch ($this->type) {
            case 'backstage_index':
                $data = $this->backstageIndexData($user);
                break;
            default:
                $data = $this->defaultData($user, $currencySet);
                break;
        }

        switch ($this->type) {
            case 'front_show':
                $data['display_currency'] = __('currency.' . strtoupper($data['currency']));
                $data['display_language'] = __('language.' . strtolower($data['language']));
                $data['country']          = strtolower($data['country']) == 'thailand' ? __('user.thailand') : $data['country'];
                break;
            case 'affiliate_show':
                $data['display_status']   = transfer_show_value($user->status, User::$affiliateStatuses);
                $data['currency']         = __('currency.' . strtoupper($data['currency']));
                $data['display_language'] = __('language.' . strtolower($data['language']));
                $data['country']          = strtolower($data['country']) == 'thailand' ? __('user.thailand') : $data['country'];
                break;
            case 'member_data_query':
                $total_deposit    = $user->deposits()->where('status', Deposit::STATUS_RECHARGE_SUCCESS)->sum('arrival_amount');
                $total_withdrawal = $user->withdrawals()->where('status', Withdrawal::STATUS_SUCCESSFUL)->sum('amount');
                # 第三方钱包总和
                $thirdPartTotalBalance        = $user->gamePlatformUsers()->sum('balance');
                $totalBalance                 = (int)$thirdPartTotalBalance + (int)$user->account->getAvailableBalance();
                $data['total_wallet_balance'] = thousands_number($totalBalance);
                $data['total_deposit']        = thousands_number($total_deposit);
                $data['total_withdrawal']     = thousands_number($total_withdrawal);
                break;
            case 'member_profileSummary':
                $first_deposit_time = '';
                $deposit            = $user->deposits()->orderBy('created_at', 'desc')->first();
                if (is_object($deposit)) {
                    $first_deposit_time = $deposit->created_at;
                }
                $data['first_deposit_time'] = convert_time($first_deposit_time);
                break;
            case 'down_line_managements':
                $data['name'] = hidden_name($data['name']);
                break;
            case 'risk_summary':
                $riskAccountSummary = $this->data['account_summary']->where('user_id', $user->id)->first();
                $riskReportSummary  = $this->data['report_summary']->where('user_id', $user->id)->first();

                $data['transaction_summary'] = [
                    'total_deposit'    => empty($riskAccountSummary->total_deposit) ? '0.00' : thousands_number($riskAccountSummary->total_deposit),
                    'total_withdrawal' => empty($riskAccountSummary->total_withdrawal) ? '0.00' : thousands_number($riskAccountSummary->total_withdrawal),
                ];
                $totalStake                  = empty($riskReportSummary->total_stake) ? 0.00 : $riskReportSummary->total_stake;
                $totalProfit                 = empty($riskReportSummary->total_profit) ? 0.00 : $riskReportSummary->total_profit;
                $totalBetNum                 = empty($riskReportSummary->total_bet_num) ? 0.00 : $riskReportSummary->total_bet_num;
                $totalBonus                  = empty($riskReportSummary->total_bonus) ? 0.00 : $riskReportSummary->total_bonus;
                $data['bet_summary']         = [
                    'total_stake'         => thousands_number($totalStake),
                    'total_profit'        => thousands_number($totalProfit),
                    'total_bet_num'       => thousands_number($totalBetNum),
                    'total_bonus'         => thousands_number($totalBonus),
                    'total_effective_bet' => empty($riskReportSummary->total_effective_bet) ? '0.00' : thousands_number($riskReportSummary->total_effective_bet),
                    'percent'             => $totalStake == 0.00 ? '0.00' : format_number($totalProfit / $totalStake, 2),
                    'per_bet'             => $totalBetNum == 0.00 ? '0.00' : format_number($totalStake / $totalBetNum, 2),
                ];
                break;
        }
        return $data;
    }

    public function includeInfo(User $user)
    {
        // viet-441 当用户风控组包含不允许弹窗领取账户安全设置奖金时，限制 info 里面的弹窗参数 is_can_claim_verify_prize 必须是 false
        // 所以这里避免不必要的查询，以及减少一次查询，user_info -> risk_group_id(users) -> risk_group，直接从 user 获取 riskGroup
        if ($this->type == 'front_show'){
            return $this->item($user->info, new UserInfoTransformer($this->type, $user->riskGroup->rules));
        }
        return $this->item($user->info, new UserInfoTransformer($this->type));
    }

    public function includeAccount(User $user)
    {
        return $this->item($user->account, new UserAccountTransformer());
    }

    public function includeUserLoginLog(User $user)
    {
        return $this->item($user->userLoginLogs, new UserLoginLogTransformer());
    }

    public function includeVip(User $user)
    {
        if (!$user->vip) {
            return null;
        }

        return $this->item($user->vip, new VipTransformer());
    }

    public function includeReward(User $user)
    {
        if (!$user->reward) {
            return null;
        }

        return $this->item($user->reward, new RewardTransformer());
    }

    public function includeGamePlatformUsers(User $user)
    {
        if ('backstage_index' == $this->type) {
            $gamePlatformUsers = $user->gamePlatformUsers()->whereNotIn('platform_code', ['IMESports'])->get();
        } else {
            $gamePlatformUsers = $user->gamePlatformUsers;
        }
        return $this->collection($gamePlatformUsers, new GamePlatformUserTransformer('backstage'));
    }

    public function includeAffiliate(User $user)
    {
        if (!$user->affiliate) {
            return null;
        }

        return $this->item($user->affiliate, new AffiliateTransformer());
    }

    public function includeUserRisks(User $user)
    {
        $risks = $user->userRisks()->orderBy('created_at', 'desc')->get();
        return $this->collection($risks, new UserRiskTransformer());
    }

    public function defaultData(User $user, $currencySet)
    {
        return  [
            'id'                       => $user->id,
            'currency'                 => $user->currency,
            'language'                 => $user->language,
            'country'                  => $currencySet ? $currencySet->country : '',
            'name'                     => $user->name,
            'parent_id'                => $user->parent_id,
            'parent_id_list'           => $user->parent_id_list,
            'parent_name'              => $user->parent_name,
            'parent_name_list'         => $user->parent_name_list,
            'notification_count'       => $user->notification_count,
            'vip_id'                   => $user->vip_id,
            'display_vip_id'           => transfer_show_value($user->vip_id, Vip::getDropList()),
            'reward'                   => $user->reward_id,
            'display_reward'           => transfer_show_value($user->reward_id, Reward::getDropList()),
            'risk_group_id'            => $user->risk_group_id,
            'display_risk_group_id'    => transfer_show_value($user->risk_group_id, RiskGroup::getDropList()),
            'payment_group_id'         => $user->payment_group_id,
            'display_payment_group_id' => transfer_show_value($user->payment_group_id, PaymentGroup::getDropList()),
            'status'                   => $user->status,
            'display_status'           => transfer_show_value($user->status, User::$statuses),
            'referral_code'            => $user->referral_code,
            'referrer_code'            => $user->referrer_code,
            'security_question'        => !empty($user->security_question) ? $user->security_question : '',
            'odds'                     => $user->odds,
            'is_agent'                 => $user->is_agent,
            'display_is_agent'         => transfer_lang_value('user', User::$agent)[$user->is_agent],
            'is_need_change_password'  => $user->is_need_change_password,
            'affiliate_code'           => $user->affiliate_code,
            'affiliated_code'          => $user->affiliated_code,
            'display_odds'             => __('dropList.' . User::$oddsForTranslation[$user->odds]),
            'created_at'               => convert_time($user->created_at),
        ];
    }

    public function backstageIndexData(User $user)
    {
        return  [
            'id'                       => $user->id,
            'currency'                 => $user->currency,
            'language'                 => $user->language,
            'name'                     => $user->name,
            'vip_id'                   => $user->vip_id,
            'display_vip_id'           => transfer_show_value($user->vip_id, Vip::getDropList()),
            'reward'                   => $user->reward_id,
            'display_reward'           => transfer_show_value($user->reward_id, Reward::getDropList()),
            'risk_group_id'            => $user->risk_group_id,
            'display_risk_group_id'    => transfer_show_value($user->risk_group_id, RiskGroup::getDropList()),
            'payment_group_id'         => $user->payment_group_id,
            'display_payment_group_id' => transfer_show_value($user->payment_group_id, PaymentGroup::getDropList()),
            'status'                   => $user->status,
            'display_status'           => transfer_show_value($user->status, User::$statuses),
            'affiliated_code'          => $user->affiliated_code,
            'created_at'               => convert_time($user->created_at),
        ];
    }
}
