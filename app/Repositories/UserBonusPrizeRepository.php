<?php
namespace App\Repositories;

use App\Models\Bonus;
use App\Models\GamePlatformProduct;
use App\Models\Report;
use App\Models\TurnoverRequirement;
use App\Models\User;
use App\Models\UserBetCountLog;
use App\Models\UserBonusPrize;
use App\Services\ReportService;
use Carbon\Carbon;

class UserBonusPrizeRepository
{
    /**
     * 检查一个组别只能使用一个红利代码[一个周期只能使用一个]
     *
     * @param Bonus $bonus
     * @param User $user
     * @return bool
     */
    public static function checkBonusGroupLimit(Bonus $bonus, User $user)
    {
        return UserBonusPrize::query()->where('bonus_group_id', $bonus->bonus_group_id)
            ->where('user_id', $user->id)
            ->whereIn('status', UserBonusPrize::$checkStatuses)
            ->exists();
    }

    public static function checkCycleLimit(Bonus $bonus, User $user)
    {
        $date = BonusRepository::findDate($bonus);
        return UserBonusPrize::query()->where('bonus_id', $bonus->id)
            ->where('user_id', $user->id)
            ->where('date', $date)
            ->whereIn('status', UserBonusPrize::$checkStatuses)
            ->exists();
    }

    /**
     * 获取未关闭的红利奖励
     *
     * @param   integer     $userId         会员id
     * @param   null        $productCode    游戏产品code
     * @return  \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getNotClosePrizes($userId, $productCode=null)
    {
        $builder = UserBonusPrize::query()->where('user_id', $userId)
            ->notClosed()
            ->whereIn('status', UserBonusPrize::$checkStatuses)
            ->orderBy('created_at');

        if (!empty($productCode)) {
            $builder->where('product_code', $productCode);

        }

        return $builder->get();
    }

    /**
     * 检查平台是否存在未关闭奖励
     *
     * @param   integer     $userId         会员id
     * @param   string      $platformCode   游戏平台code
     * @return  bool
     */
    public static function checkNotClosePrizeExists($userId, $platformCode)
    {
        $productCodes = GamePlatformProduct::getAll()->where('platform_code', $platformCode)->pluck('code')->toArray();

        return UserBonusPrize::query()->where('user_id', $userId)
            ->whereIn('product_code', $productCodes)
            ->notClosed()
            ->whereIn('status', UserBonusPrize::$checkStatuses)
            ->exists();
    }

    /**
     * 更新会员红利成功状态，并添加到统计报表
     *
     * @param UserBonusPrize $prize
     */
    public static function setSuccess(UserBonusPrize $prize)
    {
        # 红利更新状态
        if ($prize->success()) {

            # 创建流水要求
            TurnoverRequirement::add($prize, $prize->is_turnover_closed);

            $product = GamePlatformProduct::getAll()->where('code', $prize->product_code)->first();

            # 统计产品报表
            UserBetCountLog::report(
                UserBetCountLog::PREFIX_PRIZE . $prize->id,
                $prize->user->id,
                $prize->product_code,
                $prize->created_at,
                [Report::$productMappingTypes[Report::TYPE_BONUS] => $prize->prize]
            );

            # 统计平台优惠
            (new ReportService())->platformReport(
                $prize->user,
                $product->platform_code,
                Report::TYPE_PROMOTION,
                $prize->prize,
                $prize->created_at
            );
        }
    }

    /**
     * 创建红利奖励
     *
     * @param Bonus $bonus
     * @param User $user
     * @param array $currencySet
     * @param $depositAmount
     * @param $remarkId
     * @return UserBonusPrize
     */
    public static function createPrize(Bonus $bonus, User $user, $currencySet, $depositAmount, $remarkId=null)
    {
        # 记录产生奖励时的红利数据
        $set = $currencySet;
        $set['type']        = $bonus->type;
        $set['rollover']    = $bonus->rollover;
        $set['amount']      = $bonus->amount;

        $userBonusPrize = new UserBonusPrize([
            'user_id'           => $user->id,
            'user_name'         => $user->name,
            'currency'          => $user->currency,
            'bonus_id'          => $bonus->id,
            'bonus_code'        => $bonus->code,
            'bonus_group_id'    => $bonus->bonus_group_id,
            'set'               => $set,
            'category'          => $bonus->category,
            'product_code'      => $bonus->product_code,
            'deposit_amount'    => $depositAmount,
            'remark_id'         => $remarkId,
        ]);

        $maxPrize = $currencySet['max_prize'];
        # 获取奖励数值
        $prize = static::getPrizeValue($bonus, $depositAmount);

        # 如果奖励值大于最高上限值
        $userBonusPrize->is_max_prize = false;
        if ($prize >= $maxPrize) {
            $userBonusPrize->is_max_prize = true;
            $prize = $maxPrize;
        }
        $userBonusPrize->prize = $prize;

        # 获取关闭值
        $userBonusPrize->turnover_closed_value = static::getCloseValue($bonus, $currencySet, $depositAmount, $prize, $userBonusPrize->is_max_prize);

        # 关闭流水,如果关闭流水值为0,直接关闭奖励
        $userBonusPrize->is_turnover_closed = !$userBonusPrize->turnover_closed_value ? true : false;

        $userBonusPrize->status = UserBonusPrize::STATUS_CREATED;

        # 获取归属日期
        $userBonusPrize->date = BonusRepository::findDate($bonus);

        $userBonusPrize->save();

        return $userBonusPrize;
    }

    /**
     * 获取奖励值
     *
     * @param  Bonus    $bonus          红利
     * @param  float    $depositAmount  充值金额
     * @return string
     */
    public static function getPrizeValue(Bonus $bonus, $depositAmount)
    {
        $result = 0;

        $amount = $bonus->amount;

        switch ($bonus->type) {
            case Bonus::TYPE_FIXED:
                $result = $amount;
                break;
            case Bonus::TYPE_PERCENT:
                $result = $depositAmount * $amount / 100;
                break;
        }

        return format_number($result, 6);
    }

    /**
     * 获取关闭值
     * 最高奖金:
     *  1、固定值：（最小转账金额+奖金）* 流水倍数
     *  2、百分比：先计算最高奖金对应的充值金额，（最高奖金对应的充值金额 + 奖金）* 流水倍数
     * 正常奖金：
     *  （充值金额+奖金）* 流水倍数
     *
     * @param   Bonus     $bonus
     * @param   array     $currencySet
     * @param   float     $depositAmount     充值金额
     * @param   float     $prize              奖金
     * @param   bool      $isMaxPrize         是否是最高金额
     * @return float|int
     */
    public static function getCloseValue(Bonus $bonus, $currencySet, $depositAmount, $prize, $isMaxPrize)
    {
        $rollover    = $bonus->rollover;
        $minTransfer = $currencySet['min_transfer'];
        $amount      = $bonus->amount;

        if ($isMaxPrize) {
            switch ($bonus->type) {
                case Bonus::TYPE_FIXED:
                    return ($minTransfer + $prize) * $rollover;
                    break;
                case Bonus::TYPE_PERCENT:
                    $maxAmount = $prize / ($amount / 100);
                    return ($maxAmount + $prize) * $rollover;
                    break;
            }
        } else {
            return ($depositAmount + $prize) * $rollover;
        }
    }

    /**
     * 根据会员id统计在某段时间内红利总数或者未关闭的红利
     *
     * @param $userId
     * @param $endAt
     * @param null $startAt
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public static function getByUserIdAndTime($userId, $endAt, $startAt=null)
    {
        $builder = UserBonusPrize::query()->where('user_id', $userId)
            ->whereIn('status', UserBonusPrize::$checkStatuses)
            ->where('created_at', '<=', $endAt);

        if (!is_null($startAt)) {
            $builder->where('created_at', '>', $startAt);
        }

        return $builder->get();
    }

    /**
     * 获取会员时间段内某个产品的奖励构造器
     *
     * @param $userId
     * @param $startAt
     * @param $endAt
     * @param $productCode
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getBuilderByUserTimeAndProductCode($userId, $startAt, $endAt, $productCode)
    {
        $startAt = Carbon::parse($startAt)->startOfDay();
        $endAt   = Carbon::parse($endAt)->endOfDay();

        $builder = UserBonusPrize::query()->where('user_id', $userId)
            ->where('product_code', $productCode)
            ->where('status', UserBonusPrize::STATUS_SUCCESS)
            ->where('created_at', '>=', $startAt)
            ->where('created_at', '<', $endAt);

        return $builder;
    }
}