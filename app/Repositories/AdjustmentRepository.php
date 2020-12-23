<?php
namespace App\Repositories;

use App\Jobs\TransactionProcessJob;
use App\Models\Adjustment;
use App\Models\Deposit;
use App\Models\GamePlatformTransferDetail;
use App\Models\Model;
use App\Models\Remark;
use App\Models\Report;
use App\Models\TurnoverRequirement;
use App\Models\UserAccount;
use App\Models\UserBetCountLog;
use App\Services\ReportService;
use App\Services\TransactionService;
use Illuminate\Support\Facades\DB;

class AdjustmentRepository
{
    /**
     * adjustment添加帐变记录[一定包事务]
     *
     * @param Adjustment $adjustment
     * @return \App\Models\Transaction
     * @throws \Exception
     */
    public static function addTransaction(Adjustment $adjustment)
    {
        $transactionType = $adjustment->findTransactionType();

        # 创建流水要求
        if ($adjustment->isDeposit()) {
            TurnoverRequirement::add($adjustment, $adjustment->is_turnover_closed);
        }

        # 帐变记录
        $transaction = (new TransactionService())->addTransaction(
            $adjustment->user,
            $adjustment->amount,
            $transactionType,
            $adjustment->id,
            $adjustment->order_no
        );

        # 更新对应的transaction_id
        $adjustment->update(['transaction_id' => $transaction->id]);

        return $transaction;
    }

    /**
     * 根据会员id获取某时间段内调整总金额和总流水关闭值
     *
     * @param   integer         $userId     会员id
     * @param   string          $endAt      结束时间
     * @param   null            $startAt    开始时间
     * @return  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public static function getByUserIdAndTime($userId, $endAt, $startAt=null)
    {
        $builder = Adjustment::query()->where('user_id', $userId)
            ->where('status', Adjustment::STATUS_SUCCESSFUL)
            ->where('created_at', '<=', $endAt);

        if (!is_null($startAt)) {
            $builder->where('created_at', '>', $startAt);
        }

        return $builder->get();
    }

    /**
     * 第三方转账人工确认成功检查是否关联adjustment
     *
     * @param GamePlatformTransferDetail $detail
     * @return boolean
     */
    public static function checkSuccessPlatformTransferDetail(GamePlatformTransferDetail $detail)
    {
        $adjustment = Adjustment::query()->where('platform_transfer_detail_id', $detail->id)->first();

        if (!$adjustment) {
            return false;
        }

        if ($detail->isSuccess()) {

            # adjustment 提现关闭流水
            if ($adjustment->isWithdrawal()) {
                static::closeTurnoverRequirement($adjustment);
            }

            # 更新成功状态
            $adjustment->approve();
        } else {

            # 更新失败状态
            $remark = 'Transfer to ' . $adjustment->platform_code . ' failed.';
            $adjustment->fail($remark);
        }

        return true;
    }


    /**
     * 关闭adjustment流水要求
     *
     * @param Adjustment $adjustment
     */
    public static function closeTurnoverRequirement(Adjustment $adjustment)
    {
        if ($adjustment->isWithdrawal() && !empty($adjustment->related_order_no) && $related = AdjustmentRepository::getRelatedModel($adjustment->related_order_no)) {
            $related->manualCloseTurnoverRequirement();
        }
    }

    /**
     * 检查是否存在未达到流水被要求的adjustment
     *
     * @param string $platformCode
     * @param integer $userId
     * @return mixed
     */
    public static function checkNotCloseExists($userId, $platformCode)
    {
        return Adjustment::query()->where('user_id', $userId)
            ->where('platform_code', $platformCode)
            ->whereIn('category', Adjustment::$checkTurnoverClosedCategories)
            ->notClosed()
            ->whereIn('status', Adjustment::$checkStatuses)
            ->exists();
    }

    /**
     * 获取关联的model
     *
     * @param $relatedOrderNo
     * @return Deposit|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public static function getRelatedModel($relatedOrderNo)
    {
        $model = null;
        switch (substr($relatedOrderNo, 0, 1)) {
            case Model::TXN_ID_DEPOSIT:
                $model = Deposit::findByOrderNo($relatedOrderNo);
                break;
            case Model::TXN_ID_ADJUSTMENT:
                $model = Adjustment::findByOrderNo($relatedOrderNo);
                break;
        }

        return $model;
    }

    /**
     * 调整主钱包金额
     *
     * @param Adjustment    $adjustment
     * @param string        $adminName
     * @throws
     */
    public static function adjustmentMainWallet(Adjustment $adjustment, $adminName='')
    {
        try {
            $transaction = DB::transaction(function () use ($adjustment, $adminName) {

                if (static::setSuccess($adjustment, $adminName)) {

                    # 统计数据
                    static::recordReport($adjustment);

                    return AdjustmentRepository::addTransaction($adjustment);
                }

                return null;
            });
        } catch (\Exception $e) {
            error_response(422, $e->getMessage());
        }

        if ($transaction) {
            dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
        }
    }

    /**
     * 拆分统计adjustment数据
     *
     * @param Adjustment $adjustment
     */
    public static function recordReport(Adjustment $adjustment)
    {
        $service = new ReportService();
        # 1、判断是否是平台相关统计数据
        # 2、判断是否是产品相关统计数据
        # 3、剩下除不记录报表的分类就是调整相关统计数据
        if (isset(Adjustment::$mappingPlatformReportType[$adjustment->category])) {
            $type = Adjustment::$mappingPlatformReportType[$adjustment->category];
            # 类型一致为正数，不一致为负数
            if ((Report::TYPE_DEPOSIT == $type && $adjustment->isWithdrawal())
                || (Report::TYPE_WITHDRAWAL == $type && $adjustment->isDeposit()) ) {
                $amount = -1 * $adjustment->amount;
            } else {
                $amount = $adjustment->isDeposit() ? $adjustment->amount : -1 * $adjustment->amount;
            }
            # 这里要计算第三方adjustment的情况
            $platformCode = !empty($adjustment->platform_code) ?  $adjustment->platform_code : UserAccount::MAIN_WALLET;
            $service->platformReport(
                $adjustment->user,
                $platformCode,
                $type,
                $amount,
                now()
            );
        } elseif(isset(Adjustment::$mappingProductReportType[$adjustment->category])) {
            $amount = $adjustment->isDeposit() ? $adjustment->amount : -1 * $adjustment->amount;
            $field = Report::$productMappingTypes[Adjustment::$mappingProductReportType[$adjustment->category]];
            UserBetCountLog::report(
                UserBetCountLog::PREFIX_ADJUSTMENT . $adjustment->id,
                $adjustment->user->id,
                UserAccount::MAIN_WALLET,
                now(),
                [$field => $amount]
            );
        } elseif (!in_array($adjustment->category, Adjustment::$mappingProductReportType)) {
            $type = $adjustment->isDeposit() ? Report::TYPE_ADJUSTMENT_IN : Report::TYPE_ADJUSTMENT_OUT;
            $service->platformReport(
                $adjustment->user,
                UserAccount::MAIN_WALLET,
                $type,
                $adjustment->amount,
                now()
            );
        }
    }

    /**
     * 在adjustment处理成功添加要做的逻辑
     *
     * @param $adjustment
     * @param $adminName
     * @param $remark
     * @return bool
     */
    public static function setSuccess(Adjustment $adjustment, $adminName, $remark='')
    {
        # auto hold withdrawal
        if (Adjustment::CATEGORY_WELCOME_BONUS == $adjustment->category) {
            RemarkRepository::create(
                $adjustment->user_id,
                Remark::TYPE_HOLD_WITHDRAWAL,
                Remark::CATEGORY_ADJUSTMENT,
                'Welcome Bonus'
            );
        }

        return $adjustment->approve($adminName, $remark);
    }
}