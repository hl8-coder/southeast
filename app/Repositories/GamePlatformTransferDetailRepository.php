<?php
namespace App\Repositories;

use App\Jobs\CheckWaitTransferDetailJob;
use App\Models\Config;
use App\Models\GamePlatformTransferDetail;
use App\Models\Report;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Support\Facades\Log;

class GamePlatformTransferDetailRepository
{
    public static function create(
        User $user,
        $platformCode,
        $from,
        $to,
        $isIncome,
        $amount,
        $conversionAmount,
        $userCurrency,
        $platformCurrency,
        $userIp,
        $fromBeforeBalance=0,
        $toBeforeBalance=0,
        $userBonusPrizeId=null,
        $bonusAmount=0,
        $adminName=''
    ) {
        $detail = new GamePlatformTransferDetail();
        $detail->user_id                = $user->id;
        $detail->user_name              = $user->name;
        $detail->user_bonus_prize_id    = $userBonusPrizeId;
        $detail->platform_code          = $platformCode;
        $detail->from                   = $from;
        $detail->to                     = $to;
        $detail->is_income              = $isIncome;
        $detail->bonus_amount           = $bonusAmount;
        $detail->amount                 = $amount;
        $detail->from_before_balance    = $fromBeforeBalance;
        $detail->to_before_balance      = $toBeforeBalance;
        $detail->conversion_amount      = $conversionAmount;
        $detail->user_currency          = $userCurrency;
        $detail->platform_currency      = $platformCurrency;
        $detail->user_ip                = $userIp;
        $detail->admin_name             = $adminName;

        $detail->save();

        return $detail;
    }

    protected static function findAvailableOrderNo()
    {
        do {
            $orderNo = time() . random_int(10000, 99999);
        } while (GamePlatformTransferDetail::query()->where('order_no', $orderNo)->exists());

        return $orderNo;
    }

    public static function setPlatformOrderNo(GamePlatformTransferDetail $detail, $no)
    {
        $detail->update([
            'platform_order_no' => $no,
        ]);

        return $detail;
    }

    /**
     * 设定出账账户转账前金额
     *
     * @param   GamePlatformTransferDetail  $detail
     * @param   float                       $fromBeforeBalance  转账前金额
     * @return  GamePlatformTransferDetail
     */
    public static function setFromBeforeBalance(GamePlatformTransferDetail $detail, $fromBeforeBalance)
    {
        $detail->update([
            'from_before_balance' => $fromBeforeBalance,
        ]);

        return $detail;
    }

    /**
     * 设定入帐账户转账前金额
     *
     * @param   GamePlatformTransferDetail  $detail
     * @param   float                       $toBeforeBalance    转帐前金额
     * @return  GamePlatformTransferDetail
     */
    public static function setToBeforeBalance(GamePlatformTransferDetail $detail, $toBeforeBalance)
    {
        $detail->update([
            'to_before_balance' => $toBeforeBalance,
        ]);

        return $detail;
    }


    /**
     * 更新转账明细成功状态;若存在红利则更新红利派发成功
     *
     * @param GamePlatformTransferDetail $detail
     * @return GamePlatformTransferDetail
     */
    public static function setSuccess(GamePlatformTransferDetail $detail, $adminName=null, $remark='')
    {
        # 如果是成功状态就不需要再更新状态
        if ($detail->isSuccess()) {
            return $detail;
        }

        if ($detail->success($adminName, $remark)) {

            # 添加平台报表
            (new ReportService())->platformReport(
                $detail->user,
                $detail->platform_code,
                $detail->isIncome() ? Report::TYPE_TRANSFER_IN : Report::TYPE_TRANSFER_OUT,
                $detail->amount,
                $detail->created_at
            );

            # 这里和外面均没有启用事务，最好是将这个方法移动到 create 同层的位置，不影响代码执行完整性
            if ($prize = $detail->userBonusPrize) {
                UserBonusPrizeRepository::setSuccess($prize);
            }
        }

        return $detail->refresh();
    }

    /**
     * 设置失败状态
     *
     * @param  GamePlatformTransferDetail    $detail
     * @param  string                        $sysRemark      系统备注
     * @param  string                        $remark         备注
     * @param  string                        $adminName      审核管理员
     * @return GamePlatformTransferDetail
     */
    public static function setFail(GamePlatformTransferDetail $detail, $sysRemark='', $remark = '', $adminName=null)
    {
        # 如果是失败状态就不需要再更新状态
        if ($detail->isFail()) {
            return $detail;
        }

        $detail->fail($sysRemark, $remark, $adminName);

        return $detail->refresh();
    }

    /**
     * 设定等待三方状态
     *
     * @param GamePlatformTransferDetail $detail
     * @param string $sysRemark
     * @return GamePlatformTransferDetail
     */
    public static function setWaiting(GamePlatformTransferDetail $detail, $sysRemark='')
    {
        # 如果是等待状态就不需要再更新状态
        if ($detail->isChecking()) {
            return $detail;
        }

        $detail->waiting($sysRemark);

        return $detail->refresh();
    }

    /**
     * 设定检查状态
     *
     * @param GamePlatformTransferDetail $detail
     * @return int
     */
    public static function setChecking(GamePlatformTransferDetail $detail)
    {
        return $detail->checking();
    }

    /**
     * 人工确认状态
     *
     * @param GamePlatformTransferDetail $detail
     * @param string $sysRemark
     * @return GamePlatformTransferDetail
     */
    public static function setWaitManualConfirm(GamePlatformTransferDetail $detail, $sysRemark='')
    {
        $detail->waitManualConfirm($sysRemark);

        return $detail->refresh();
    }

    public static function getWaitingDetails()
    {
        return GamePlatformTransferDetail::query()->waiting()->lastest()->get();
    }

    /**
     * 设定等待三方状态，并添加检查队列
     *
     * @param GamePlatformTransferDetail $detail
     * @return
     */
    public static function setWaitingAndAddCheckJob(GamePlatformTransferDetail $detail) {
        $detail = static::setWaiting($detail);
        static::addCheckJob($detail);
        return $detail;
    }

    /**
     * 添加检查队列
     * 1、更新状态为checking
     * 2、放入检查队列中
     *
     * @param GamePlatformTransferDetail $detail
     */
    public static function addCheckJob(GamePlatformTransferDetail $detail)
    {
        $noCheckFunctionPlatforms = ['RTG'];

        if (in_array($detail->platform_code, $noCheckFunctionPlatforms)) {
            $detail->waitManualConfirm('Check Job');
        } else {
            # 获取最新状态
            $detail = $detail->refresh();
            if (static::setChecking($detail)) {
                dispatch(new CheckWaitTransferDetailJob($detail))->onQueue('check_waiting_transfer');
            }
        }
    }

    public static function isExceedCheckLimit(GamePlatformTransferDetail $detail)
    {
        $limit = Config::findValue('game_platform_transfer_check_limit');

        if ($limit) {
            return $detail->check_times >= $limit;
        }

        return false;
    }

}
