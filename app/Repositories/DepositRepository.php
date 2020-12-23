<?php

namespace App\Repositories;

use App\Models\Currency;
use App\Models\Deposit;
use App\Models\User;
use App\Models\PaymentPlatform;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Boolean;

class DepositRepository
{
    /**
     * 设定完成订单不显示
     */
    private static function _setCompletedNoShow($ORM)
    {
        return $ORM->where('tag', '<>', Deposit::TAG_CLOSED);
    }


    public static function getOpenDeposit($request)
    {
        $ORM = Deposit::query()->where(function($query) {
            $query->whereIn("status", [Deposit::STATUS_HOLD, Deposit::STATUS_CREATED])
                ->orWhere("need_second_approve", 1);
        });

        if (isset($request->filter["status"])) {
            if (!in_array('all', $request->filter["status"])) {
                $ORM->whereIn("status", $request->filter["status"]);
            }
        }

        return $ORM->orderBy('id', 'desc');

    }

    public static function getFastDeposit($request)
    {
        $ORM = Deposit::query()->whereNotIn("button_flow_code", [
            "1.2.2.1.2", "1.2.2.2.2",
        ]);

        if (isset($request->filter["status"])) {
            if (!in_array('all', $request->filter["status"])) {
                $ORM->whereIn("status", $request->filter["status"]);
            }
        }

        return $ORM->orderBy("id", "desc")
            ->whereIn('payment_type', [
                PaymentPlatform::PAYMENT_TYPE_BANKCARD,
                PaymentPlatform::PAYMENT_TYPE_MPAY,
                PaymentPlatform::PAYMENT_TYPE_LINEPAY,
            ]);
    }

    public static function getGateway($request)
    {
        $ORM = Deposit::query();

        // if($request->input("filter.status") === null) {
        //     $ORM = self::_setCompletedNoShow($ORM);
        // }

        if (isset($request->filter["status"])) {
            if (!in_array('all', $request->filter["status"])) {
                $ORM->whereIn("status", $request->filter["status"]);
            }
        }

        return $ORM->orderBy("id", "desc")->whereNotIn('payment_type', [
            PaymentPlatform::PAYMENT_TYPE_BANKCARD,
            PaymentPlatform::PAYMENT_TYPE_MPAY,
            PaymentPlatform::PAYMENT_TYPE_LINEPAY,
        ]);
    }

    public static function getAdvanceCredit($request)
    {
        $ORM = Deposit::query()->whereIn("button_flow_code", [
            "1.2.2.1.2.1", "1.2.2.2.2.1", "1.2.2.1.2", "1.2.2.2.2", "4",
        ]);

        return $ORM->orderBy("id", "desc");
    }

    /**
     * 获取时间段内充值成功笔数
     *
     * @param User $user
     * @param $startAt
     * @param $endAt
     * @return mixed
     */
    public static function getSuccessDepositCount(User $user, $startAt, $endAt)
    {
        return Deposit::query()->where('user_id', $user->id)
            ->startAt($startAt)
            ->endAt($endAt)
            ->success()
            ->count();
    }

    /**
     * 获取未关闭的充值单
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getNotCloseDeposits()
    {
        return Deposit::query()->where('status', Deposit::STATUS_RECHARGE_SUCCESS)
            ->where('is_turnover_limit_closed', false)
            ->get();
    }

    /**
     * 根据会员id获取某时间段内充值流水要求
     *
     * @param integer $userId 会员id
     * @param string $endAt 结束时间
     * @param null $startAt 开始时间
     * @return  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public static function getByUserIdAndTime($userId, $endAt, $startAt = null)
    {
        $builder = Deposit::query()->where('user_id', $userId)
            ->where('status', Deposit::STATUS_RECHARGE_SUCCESS)
            ->where('approved_at', '<=', $endAt);

        if (!is_null($startAt)) {
            $builder->where('approved_at', '>', $startAt);
        }

        return $builder->get();
    }

    /**
     * 检查充值限制
     *
     * @param   string  $currency           币别
     * @param   integer $paymentPlatformId  支付id
     * @param   float   $amount             金额
     * @return bool
     */
    public static function checkDepositLimit($currency, $paymentPlatformId, $amount)
    {
        $paymentPlatform = PaymentPlatform::query()->find($paymentPlatformId);
        $currency        = Currency::query()->where('code', $currency)->first();
        $minDeposit      = $currency->min_deposit > $paymentPlatform->min_deposit ? $currency->min_deposit : $paymentPlatform->min_deposit;
        if ($amount < $minDeposit) {
            $showMinDeposit = 'VND' == $currency->code ? thousands_number($minDeposit, 3) : thousands_number($minDeposit);
            error_response(422, __('deposit.DEPOSIT_AMOUNT_LESS_THAN_DEPOSIT_MIN_LIMIT', ['amount' => $showMinDeposit . ' ' . $currency->code]));
        }

        # 上限值需要检查下是否为0
        if (!empty($currency->max_deposit) && empty($paymentPlatform->max_deposit)) {
            $maxDeposit = $currency->max_deposit;
        } elseif (empty($currency->max_deposit) && !empty($paymentPlatform->max_deposit)) {
            $maxDeposit = $paymentPlatform->max_deposit;
        } else {
            $maxDeposit = $currency->max_deposit > $paymentPlatform->max_deposit ? $paymentPlatform->max_deposit : $currency->max_deposit;
        }

        if (!empty($maxDeposit) && $amount > $maxDeposit) {
            $showMaxDeposit = 'VND' == $currency->code ? thousands_number($maxDeposit, 3) : thousands_number($maxDeposit);
            error_response(422, __('deposit.DEPOSIT_AMOUNT_MORE_THAN_DEPOSIT_MAX_LIMIT', ['amount' => $showMaxDeposit . ' ' . $currency->code]));
        }

        return true;
    }

    /**
     * 检查充值pending次数
     *
     * @param   User $user
     * @return  bool
     */
    public static function checkDepositPendingLimit(User $user)
    {
        $count    = Deposit::where('user_id', $user->id)->where('status', Deposit::STATUS_CREATED)->count();
        $currency = Currency::findByCodeFromCache($user->currency);
        if (!empty($currency->deposit_pending_limit) && $count >= $currency->deposit_pending_limit) {
            error_response(422, __('deposit.exceed_the_pending_orders_limit', ['limit' => $currency->deposit_pending_limit]));
        }
        return true;
    }

}
