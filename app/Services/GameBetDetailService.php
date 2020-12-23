<?php

namespace App\Services;

use App\Models\Adjustment;
use App\Models\Deposit;
use App\Models\Game;
use App\Models\GameBetDetail;
use App\Models\Report;
use App\Models\TransferDetail;
use App\Models\TurnoverRequirement;
use App\Models\User;
use App\Models\UserBonusPrize;

class GameBetDetailService
{
    /**
     * 1、记录总流水、总盈亏
     * 2、记录有效流水、有效盈亏
     *  2.1 记录用于计算积分的流水
     *  2.2 记录用于计算赎返盈亏
     * 3、记录关闭红利流水
     * 4、记录关闭赎返流水
     * 5、记录用于计算积分流水
     *
     * @param GameBetDetail $detail
     * @param array  $data
     * @throws
     */
    public function process(GameBetDetail &$detail, array &$data)
    {
        if (!$user = $detail->user) {
            return;
        }

        if (!$game = Game::findByPlatformAndCode($detail->platform_code, $detail->game_code)) {
            throw new \Exception(__('gamePlatform.NO_CORRESPONDING_GAME'));
        }

        # 未开奖订单记录未开奖数据
        if ($detail->isBetWaiting()) {
            $data['open_bet'] = $detail->user_stake;
        }

        # 记录注单数
        $data['bet_num'] = 1;

        # 检查投注是否成功
        if (!$detail->isBetSuccess()) {
            return;
        }

        # 根据游戏设定值判断有效投注
        $detail = $this->setEffectiveBet($game, $detail);

        # 统计数据
        # 总投注
        $data['stake'] = $detail->stake;
        # 总盈亏
        $data['profit'] = $detail->profit;
        # 有效投注
        $data['effective_bet'] = $detail->available_bet;
        # 有效盈亏
        $data['effective_profit'] = $detail->available_profit;

        # 没有有效投注直接退出
        if (empty($detail->available_bet)) {
            return;
        }

        # 检查需要流水关闭项
        $detail = $this->checkNotCloseTurnoverRequirement($user, $game, $detail, $data);

        # 记录返点流水
        if ($game->isCalculateRebate()) {
            $data['calculate_rebate_bet'] = $detail->available_rebate_bet;
        }
    }

    /**
     * 检查并关闭流水要求项
     *
     * @param   User          $user
     * @param   Game          $game
     * @param   GameBetDetail $detail
     * @param   array         $data
     * @return  GameBetDetail $detail
     */
    public function checkNotCloseTurnoverRequirement(User $user, Game $game, GameBetDetail $detail, array &$data)
    {
        $availableBet       = $detail->available_bet;
        $availableRebateBet = $detail->available_bet;
        foreach (TurnoverRequirement::getNotCloseRequirement($user->id) as $requirement) {
            # 检查时间
            if ($detail->payout_at < $requirement->created_at) {
                continue;
            }

            $model = $requirement->requireable;
            if (!$model) {
                continue;
            }

            # 如果model流水已经关闭直接关闭requirement
            if ($model->isTurnoverRequirementClosed()) {
                $requirement->close();
                continue;
            }

            # 检查游戏类型流水是否能关闭模型
            if (!$this->isCanClose($game, $model)) {
                continue;
            }

            # 判断场馆是否符合
            if (!$this->isMeetPlatform($game, $model)) {
                continue;
            }

            # 剩余值 = 投注明细可用流水 + 未关闭当前流水值 - 未关闭关闭所需值
            $remainValue = $availableBet + $model->turnover_current_value - $model->turnover_closed_value;

            # 剩余值如果大于等于0，关闭对应模型
            if ($remainValue > 0) {

                # 关闭流水要求
                $model->closeTurnoverRequirement();

                # 需要使用的关闭值
                $closeValue = $availableBet - $remainValue;

                # 添加日志
                $detail->addTraceLog($model, $closeValue);

                # 统计关闭投注
                $this->recordCloseBet($data, $model, $closeValue);

                # 可用返点流水，减去关闭所需值
                $availableRebateBet = $this->setAvailableRebateBet($model, $availableRebateBet, $closeValue);

                # 存在剩余流水继续关闭其他model
                $availableBet = $remainValue;
                continue;
            } else {
                if (0 == $remainValue) {
                    # 关闭奖励
                    $model->closeTurnoverRequirement();
                } else {
                    # 更新进度
                    $model->incrementCurrentValue($availableBet);
                }

                # 统计关闭投注
                $this->recordCloseBet($data, $model, $availableBet);

                # 此处是减去可用流水
                $availableRebateBet = $this->setAvailableRebateBet($model, $availableRebateBet, $availableBet);

                # 关闭投注明细
                if ($this->closeGameBetDetail($detail, $model, $availableBet)) {
                    $availableBet = 0;
                }

                # 无可用有效流水跳出循环
                break;
            }
        }

        # 更新投注明细可用有效流水
        $detail->update([
            'available_bet'         => $availableBet,
            'available_rebate_bet'  => $availableRebateBet,
        ]);

        return $detail;
    }

    /**
     * 检查游戏类型流水是否能关闭模型
     *
     * @param Game $game
     * @param $model
     * @return bool|mixed
     */
    public function isCanClose(Game $game, $model)
    {
        if ($model instanceof Adjustment) {
            return $game->isCloseAdjustment();
        } elseif ($model instanceof UserBonusPrize) {
            return $game->isCloseBonus();
        } elseif ($model instanceof Deposit) {
            return $game->isCloseDeposit();
        } elseif ($model instanceof TransferDetail) {
            return true;
        }
        return false;
    }

    /**
     * 检查场馆是否正确
     *
     * @param Game $game
     * @param $model
     * @return bool|mixed
     */
    public function isMeetPlatform(Game $game, $model)
    {
        if ($model instanceof Adjustment) {
            if (!empty($model->product_code)) {
                return $model->product_code == $game->product_code;
            }
            if (!empty($model->platform_code)) {
                return $model->platform_code == $game->platform_code;
            }
        } elseif ($model instanceof UserBonusPrize && !empty($model->product_code)) {
            return $model->bonus->product_code == $game->product_code;
        }

        return true;
    }

    /**
     * 设置可用于可返点投注(目前只有红利会减少可返点投注)
     *
     * @param $model
     * @param float     $availableRebateBet     可用返点投注
     * @param float     $amount                 被减数
     * @return int
     */
    public function setAvailableRebateBet($model, $availableRebateBet, $amount)
    {
        if ($model instanceof UserBonusPrize) {
            return round((float)$availableRebateBet - (float)$amount, 4);
        } else if ($model instanceof Adjustment && in_array($model->category, [
            Adjustment::CATEGORY_WELCOME_BONUS,
            Adjustment::CATEGORY_RETENTION,
            Adjustment::CATEGORY_PROMOTION,
            ])) {
            return round((float)$availableRebateBet - (float)$amount, 4);
        }

        return $availableRebateBet;
    }

    /**
     * 关闭投注明细
     *
     * @param GameBetDetail $detail
     * @param object        $model              追踪model
     * @param float         $availableBet        可用投注
     * @return bool
     */
    public function closeGameBetDetail(GameBetDetail $detail, $model, $availableBet)
    {
        # 关闭投注详细
        if ($detail->close()) {
            # 添加日志
            $detail->addTraceLog($model, $availableBet);

            return true;
        }

        return false;
    }


    /**
     * 记录关闭型流水
     *
     * @param $model
     * @param $data
     * @param $value
     */
    public function recordCloseBet(&$data, $model, $value)
    {
        if (!empty($model::$reportMappingType)) {
            $k = Report::$productMappingTypes[$model::$reportMappingType];
            if (!empty($data[$k])) {
                $data[$k] += $value;
            } else {
                $data[$k] = $value;
            }
        }
    }

    /**
     * 根据游戏设定值更新有效投注
     *
     * @param Game $game
     * @param GameBetDetail $detail
     * @return GameBetDetail
     */
    public function setEffectiveBet(Game $game, GameBetDetail $detail)
    {
        if (!$game->isEffectiveBet()) {
            $detail->update([
                'available_bet' => 0
            ]);
        }

        return $detail;
    }
}
