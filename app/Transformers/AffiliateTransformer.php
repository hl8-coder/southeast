<?php

namespace App\Transformers;

use App\Models\Adjustment;
use App\Models\Affiliate;
use App\Models\Deposit;
use App\Models\TrackingStatistic;
use App\Models\TrackingStatisticLog;
use App\Models\Url;
use App\Models\User;
use App\Repositories\UserProductDailyRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(
 *   schema="Affiliate",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="代理id"),
 *   @OA\Property(property="number", type="string", description="代理序号"),
 *   @OA\Property(property="code", type="string", description="代理序号"),
 *   @OA\Property(property="web_url", type="string", description="代理网站"),
 *   @OA\Property(property="refer_by_code", type="string", description="上级代码"),
 *   @OA\Property(property="is_fund_open", type="string", description="是否开启转帐"),
 *   @OA\Property(property="commission_setting", type="string", description="分红设定"),
 *   @OA\Property(property="click", type="string", description="点击功能次数"),
 *   @OA\Property(property="new_sign_count", type="string", description="新注册人数"),
 *   @OA\Property(property="new_sign_deposit_count", type="string", description="新注册充值人数"),
 *   @OA\Property(property="total_member", type="string", description="会员数"),
 *   @OA\Property(property="active_member", type="string", description="活跃会员数"),
 *   @OA\Property(property="transaction", type="string", description="会员交易资讯"),
 *   @OA\Property(property="sub_transaction", type="string", description="下级会员交易资讯"),
 *   @OA\Property(property="user", description="会员", ref="#/components/schemas/User"),
 *   @OA\Property(property="userInfo", description="会员详情", ref="#/components/schemas/UserInfo"),
 *   @OA\Property(property="bankAccount", description="会员银行卡", ref="#/components/schemas/UserBankAccount"),
 *   @OA\Property(property="commissions", description="代理分紅记录", ref="#/components/schemas/AffiliateCommission")
 * )
 */
class AffiliateTransformer extends Transformer
{
    protected $availableIncludes = ['user', 'userInfo', 'userAccount', 'bankAccount', 'remarks', 'commissions', 'parentUser'];

    public function transform(Affiliate $affiliate)
    {

        $data = [
            'id'                     => $affiliate->id,
            'number'                 => $affiliate->code,
            'code'                   => $affiliate->code,
            'web_url'                => $affiliate->user->info->web_url,
            'refer_by_code'          => $affiliate->refer_by_code,
            'is_fund_open'           => $affiliate->is_fund_open,
            'cs_status'              => $affiliate->cs_status,
            'display_cs_status'      => transfer_show_value($affiliate->cs_status, Affiliate::$csStatuses),
            'commission_setting'     => $affiliate->commission_setting,
            'balance'                => thousands_number($affiliate->user->account->getAvailableBalance()),
            'admin_name'             => $affiliate->admin_name,
            'cal_started_at'         => $affiliate->cal_started_at,
            'cal_ended_at'           => $affiliate->cal_ended_at,
        ];

        switch ($this->type) {
            case 'backstage_index':
                return $data;
                break;
            case 'front_index':
                return collect($data)->only(['code', 'web_url'])->toArray();
                break;
        }

        # 获取代理的下级会员id array
        $subUserIds = $affiliate->subUsers()->pluck('id');
        # total Member except inactive
        $totalMember = $affiliate->subUsers()->where(
            'status', User::STATUS_ACTIVE
        )->pluck('id');
        # 当月下级会员
        $monthSubUsers       = $affiliate->subUsers()
            ->where("created_at", ">=", Carbon::now()->startOfMonth()->toDateTimeString())
            ->where("created_at", "<=", Carbon::now()->endOfMonth()->toDateTimeString());
        $newSignCount        = $monthSubUsers->count();
        $currencySubUserIds  = $monthSubUsers->pluck('id');
        $newSignDepositUserIds = Deposit::query()->whereIn('user_id', $currencySubUserIds)
            ->where('status', Deposit::STATUS_RECHARGE_SUCCESS)
            ->where("created_at", ">=", Carbon::now()->startOfMonth()->toDateTimeString())
            ->where("created_at", "<=", Carbon::now()->endOfMonth()->toDateTimeString())
            ->select(DB::raw('DISTINCT user_id'))
            ->get()
            ->pluck('user_id')
            ->toArray();

        $newSignAdjustmentDepositUserIds= Adjustment::query()->whereIn('user_id', $currencySubUserIds)
            ->where('status', Adjustment::STATUS_SUCCESSFUL)
            ->where('category', Adjustment::CATEGORY_DEPOSIT)
            ->where("created_at", ">=", Carbon::now()->startOfMonth()->toDateTimeString())
            ->where("created_at", "<=", Carbon::now()->endOfMonth()->toDateTimeString())
            ->select(DB::raw('DISTINCT user_id'))
            ->get()
            ->pluck('user_id')
            ->toArray();
        # 获取下级会员的当月日报表
        $ORM        = UserProductDailyRepository::currentMonth($subUserIds);
        $profitInfo = $ORM->get();
        # 下级活跃会员
        $activeMember = $ORM->groupBy('user_name')->select(DB::raw('user_name'))->get()->toArray();
        # 获取代理的下级代理id array
        $subAffiliate = $affiliate->subAffiliates()->pluck('id');
        # 获取下级代理会员的id array
        $subAffiliateUserIds = UserProductDailyRepository::getSubUserIds($subAffiliate);
        # 获取下级代理会员的id当月日报表
        $subORM        = UserProductDailyRepository::currentMonth($subAffiliateUserIds);
        $subProfitInfo = $subORM->get();
        # 下级活跃会员
        $subActiveMember       = $subORM->groupBy('user_name')->select(DB::raw('user_name'))->get()->toArray();
        $suffix                = Url::$suffix[1];
        $sub_member_pc_url     = Url::where([
            [
                'currencies', 'like', '%' . $affiliate->user->currency . '%'
            ],
            [
                'type', Url::TYPE_MEMBER
            ],
            [
                'device', User::DEVICE_PC
            ]
        ])
            ->first();
        $sub_member_mobile_url = Url::where([
            [
                'currencies', 'like', '%' . $affiliate->user->currency . '%'
            ],
            [
                'type', Url::TYPE_MEMBER
            ],
            [
                'device', User::DEVICE_MOBILE
            ]
        ])
            ->first();
        $inviteSubAffLink      = Url::where([
            [
                'currencies', 'like', '%' . $affiliate->user->currency . '%'
            ],
            [
                'type', Url::TYPE_AFFILIATE
            ],
            [
                'device', User::DEVICE_PC
            ]
        ])
            ->first();
        # 获取代理的Tracking
        $tracking = $affiliate->trackingStatistics()->pluck('id')->toArray();
        $click    = TrackingStatisticLog::query()
            ->where("created_at", ">=", Carbon::now()->startOfMonth()->toDateTimeString())
            ->where("created_at", "<=", Carbon::now()->endOfMonth()->toDateTimeString())
            ->where('affiliate_code', $affiliate->code)
            ->whereIn('tracking_id', $tracking)
            ->groupBy('ip')
            ->select('ip')
            ->get();

        $track = TrackingStatistic::query()->where('tracking_name', $affiliate->code)->first();
        $tid   = '';
        if (is_object($track)) {
            $tid = $track->id;
        }

        $data['invite_sub_aff_url']     = empty($inviteSubAffLink) ? '' : $inviteSubAffLink->address . $suffix;
        $data['sub_member_pc_url']      = empty($sub_member_pc_url) ? '' : $sub_member_pc_url->address . $suffix;
        $data['sub_member_mobile_url']  = empty($sub_member_mobile_url) ? '' : $sub_member_mobile_url->address . $suffix;
        $data['url']                    = empty($sub_member_pc_url) ? '' : $sub_member_pc_url->address;
        $data['tid']                    = $tid;
        $data['click']                  = count($click);
        $data['new_sign_count']         = $newSignCount;
        $data['new_sign_deposit_count'] = count(array_unique(array_merge($newSignDepositUserIds, $newSignAdjustmentDepositUserIds)));
        $data['total_member']           = count($totalMember);
        $data['active_member']          = count($activeMember);
        $data['transaction']            = [
            'platform_profit' => thousands_number($profitInfo->sum("profit") * -1),
            'user_bet'        => thousands_number($profitInfo->sum("stake")),
        ];
        $data['sub_transaction']        = [
            'platform_profit' => thousands_number($subProfitInfo->sum("effective_profit")),
            'active_member'   => count($subActiveMember),
        ];

        $language = app()->getLocale();
        switch ($language){
            case 'vi-VN':
                $language = 'vn';
                break;
            case 'en-US':
                $language = 'en';
                break;
        }

        // viet-406-correct 要求后端 affiliate/{affiliate} 显示时显示三种语言
        if ($this->type == 'backstage_show_item') {
            $languageList = ['VND' => '/vn', 'USD' => '/en', 'THB' => '/th'];
            foreach (['invite_sub_aff_url', 'sub_member_pc_url', 'sub_member_mobile_url'] as $urlKey) {
                if (empty($data[$urlKey])) {
                    continue;
                }
                $urlList    = [];
                foreach ($languageList as $key => $languageItem) {
                    $urlInfo     = parse_url($data[$urlKey]);
                    if (strstr($urlInfo['path'], $languageItem . '/') === false){
                        $urlList[] = [
                            'url'    => str_replace($urlInfo['path'], "{$languageItem}" . $urlInfo['path'], $data[$urlKey]),
                            'detail' => $key,
                        ];
                    }else{
                        $urlList[] = [
                            'url'    => $data[$urlKey],
                            'detail' => $key,
                        ];
                    }

                }
                $data[$urlKey] = $urlList;
            }
        } else {
            foreach (['invite_sub_aff_url', 'sub_member_pc_url', 'sub_member_mobile_url'] as $urlKey) {
                if (empty($data[$urlKey])) {
                    continue;
                }
                $url = parse_url($data[$urlKey]);
                preg_match_all('#(' . implode('|', ['/vn/', '/en/', '/th/']) . ')#', $url['path'], $wordsFound);
                $wordsFound = array_unique($wordsFound[0]);
                if (count($wordsFound) <= 0) {
                    $data[$urlKey] = str_replace($url['path'], "/{$language}" . $url['path'], $data[$urlKey]);
                }
            }
        }

        return $data;
    }

    public function includeUser(Affiliate $affiliate)
    {

        return $this->item($affiliate->user, new UserTransformer('affiliate_show'));
    }

    public function includeUserInfo(Affiliate $affiliate)
    {

        return $this->item($affiliate->user->info, new UserInfoTransformer($this->type));
    }

    public function includeUserAccount(Affiliate $affiliate)
    {

        return $this->item($affiliate->user->account, new UserAccountTransformer());
    }

    public function includeBankAccount(Affiliate $affiliate)
    {

        if ($affiliate->bankAccount) {
            return $this->item($affiliate->bankAccount, new UserBankAccountTransformer('affiliate_bank_account'));
        } else {
            return null;
        }
    }

    public function includeRemarks(Affiliate $affiliate)
    {
        return $this->collection($affiliate->remarks, new AffiliateRemarkTransformer());
    }

    public function includeCommissions(Affiliate $affiliate)
    {
        $commissions = $affiliate->commissions()->latest('id')->take(5)->get();

        return $this->collection($commissions, new AffiliateCommissionTransformer());
    }

    public function includeParentUser(Affiliate $affiliate)
    {

        if ($affiliate->user->parentUser) {
            return $this->item($affiliate->user->parentUser, new UserTransformer());
        } else {
            return null;
        }
    }
}
