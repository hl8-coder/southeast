<?php

namespace App\Services;

use App\Jobs\TransactionProcessJob;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\FreezeLog;
use App\Models\GamePlatform;
use App\Models\GamePlatformTransferDetail;
use App\Models\GamePlatformUser;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAccount;
use App\Repositories\GamePlatformTransferDetailRepository;
use App\Repositories\GamePlatformUserRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GamePlatformService
{
    protected $platform;
    protected $platformClass;

    public function __call($method, $arguments)
    {
        $this->platform         = $arguments[1];
        $class                  = "App\\GamePlatforms\\" . strtoupper($this->platform->code) . 'Platform';
        $this->platformClass    = new $class($arguments);

        if (!$this->platform->isEnable()) {
            error_response(403, __('gamePlatform.PLATFORM_MAINTENANCE'));
        }

        if (!method_exists($this->platformClass, $method)) {
            error_response(422, __('gamePlatform.FUNCTION_NOT_ACTIVATED'));
        }

        try {
            return call_user_func([$this->platformClass, $method]);
        } catch (\Exception $e) {
            if ($e->getCode() == 415) {
                error_response($e->getCode(), $e->getMessage(), $e->getCode());
            } else {
                error_response(422, $e->getMessage());
            }
        }
    }

    /**
     * 转入第三方平台
     *
     * 1、判断是否存在红利代码，存在则创建红利奖励
     * 2、创建三方转账明细
     * 3、冻结账户金额
     * 4、如2，3步任一失败，设置红利奖励为失败状态
     * 5、发起第三方转账
     * 6、如5明确失败，解冻会员金额
     * 7、如转账成功,在更新转账明细成功的同时会更新红利的成功[红利的更新写在转账更新中，考虑人工处理问题]
     *
     * @param   GamePlatform    $platform
     * @param   User            $user
     * @param   float           $amount         转账金额
     * @param   string          $ip
     * @param   string          $bonusCode      红利代码
     * @param   string          $adminName      管理员名称
     * @return  mixed
     * @throws
     */
    public function transferIn(GamePlatform $platform, User $user, $amount, $ip = '', $bonusCode = '', $adminName = '')
    {
        # 检查账户余额是否足够
        if (!$user->account->isBalanceEnough($amount)) {
            Log::info('name: ' . $user->name . ', 追踪信息: 77');
            error_response(422, __('userAccount.BALANCE_NOT_ENOUGH'));
        }

        $amount = format_number(abs($amount), 2);

        $userAccount = $user->account;

        $userBonusPrize = null;

        try {
            $detail = DB::transaction(function () use (
                $user,
                &$userAccount,
                &$userBonusPrize,
                $platform,
                $bonusCode,
                $amount,
                $ip,
                $adminName
            ) {
                # 如果存在红利代码，创建红利奖励，状态为"created"，
                # 在转账之后，转账成功那么转账状态需要改变，同时也会改变对应的用户红利状态为领取成功
                # 红利发放状态的改变，在转账成功的方法里
                $userBonusPrize = $bonusCode ? (new BonusService)->store($platform, $user, $bonusCode, $amount) : null;

                $userBonusPrizeId = $userBonusPrize ? $userBonusPrize->id : null;
                $userBonusPrizeAmount = $userBonusPrize ? $userBonusPrize->prize : 0;
                # 不存在红利: 转入金额 = 转入金额
                # 存在红利: 转入金额 = 转入金额+红利奖励
                $transferAmount = $amount + $userBonusPrizeAmount;

                $fromBeforeBalance = $userAccount->getAvailableBalance();
                # 第三方游戏转账明细
                $detail = $this->addTransferDetail(
                    $user,
                    $platform,
                    UserAccount::MAIN_WALLET,
                    $platform->code,
                    true,
                    $transferAmount,
                    $ip,
                    $fromBeforeBalance,
                    0,
                    $userBonusPrizeId,
                    $userBonusPrizeAmount,
                    $adminName
                );

                # 冻结金额
                UserAccountRepository::freeze($userAccount, $amount, FreezeLog::TYPE_GAME_PLATFORM_TRANSFER, $detail->id);

                return $detail;
            });
        } catch (\Exception $e) {
            error_response(422, $e->getMessage());
        }

        try {
            # 第三方转入
            $detail = $this->transfer($user, $platform, ['detail' => $detail]);
        } catch (\Exception $e) {
            GamePlatformTransferDetailRepository::setWaitingAndAddCheckJob($detail);
        }

        $detail = $this->transferInAfterDo($detail, $user, $userBonusPrize);

        return $this->transferResponse($detail);
    }

    /**
     * 转入之后处理
     *
     * @param   GamePlatformTransferDetail $detail
     * @param   User $user
     * @param   $userBonusPrize
     * @return  GamePlatformTransferDetail
     */
    public function transferInAfterDo(GamePlatformTransferDetail $detail, User $user, $userBonusPrize)
    {
        $userAccount = $user->account;

        # 确认成功，解冻并添加帐变
        if ($detail->isSuccess()) {
            try {
                $transaction = (new TransactionService())->unfreezeAndAddTransaction(
                    $userAccount,
                    $detail->getTransferAmount(),
                    Transaction::TYPE_GAME_TRANSFER_OUT,
                    FreezeLog::TYPE_GAME_PLATFORM_TRANSFER,
                    $detail->id,
                    $detail->order_no
                );
            } catch (\Exception $e) {
                # 转为人工处理
                GamePlatformTransferDetailRepository::setWaitManualConfirm($detail, $e->getMessage());
            }

            if (!empty($transaction)) {
                dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
            }
        } elseif ($detail->isFail()) { # 确认转入失败，解冻金额
            UserAccountRepository::unfreeze($userAccount, $detail->getTransferAmount(), FreezeLog::TYPE_GAME_PLATFORM_TRANSFER, $detail->id);
            if ($userBonusPrize) {
                $userBonusPrize->fail();
            }
        }

        return $detail;
    }

    /**
     * 第三方平台转出
     *
     * 1、发起第三方转出
     * 2、明确第三方成功
     *
     * @param   GamePlatform    $platform
     * @param   User            $user
     * @param   float           $amount                 转账金额
     * @param   string          $ip                     ip
     * @param   string          $adminName              管理员名称
     * @param   boolean         $isInternalTransfer     是否是内部转账，是内部转账的话需要直接帐变成功
     * @param   boolean         $isCheckTurnover        是否检查流水关闭
     * @return GamePlatformTransferDetail
     */
    public function transferOut(GamePlatform $platform, User $user, $amount, $ip = '', $adminName = '', $isInternalTransfer = false, $isCheckTurnover=true)
    {
        $amount = format_number($amount, 2);
        $userAccount = $user->account;
        $toBeforeBalance = $userAccount->getAvailableBalance();
        # 检查第三方是否有未关闭的奖励
        if ($isCheckTurnover && UserRepository::checkNotCloseTurnoverExists($user->id, $platform->code)) {
            error_response(422, __('gamePlatform.UNCLOSED_BONUS'));
        }

        # 第三方游戏转账明细
        $detail = $this->addTransferDetail(
            $user,
            $platform,
            $platform->code,
            UserAccount::MAIN_WALLET,
            false,
            $amount,
            $ip,
            0,
            $toBeforeBalance,
            null,
            0,
            $adminName
        );

        try {
            # 第三方转出
            $detail = $this->transfer($user, $platform, ['detail' => $detail]);
        } catch (\Exception $e) {
            if ($e->getCode() == 415) {
                GamePlatformTransferDetailRepository::setFail($detail, $e->getMessage());
                Log::info('name: ' . $user->name . ', 追踪信息: 235');
                error_response(422, __('userAccount.BALANCE_NOT_ENOUGH'));
            } else {
                GamePlatformTransferDetailRepository::setWaitingAndAddCheckJob($detail);
            }
        }

        $detail = $this->transferOutAfterDo($detail, $user, $isInternalTransfer);

        return $this->transferResponse($detail);
    }

    /**
     * 转出后处理
     *
     * @param GamePlatformTransferDetail $detail
     * @param User $user
     * @param $isInternalTransfer
     * @return GamePlatformTransferDetail
     */
    public function transferOutAfterDo(GamePlatformTransferDetail $detail, User $user, $isInternalTransfer)
    {
        try {
            $transaction = null;
            if ($detail->isSuccess()) {
                $transaction = DB::transaction(function () use ($user, $detail, $isInternalTransfer) {
                    return (new TransactionService())->addTransaction(
                        $user,
                        $detail->amount,
                        Transaction::TYPE_GAME_TRANSFER_IN,
                        $detail->id,
                        $detail->order_no,
                        $isInternalTransfer
                    );
                });
            }
        } catch (\Exception $e) {
            # 如果这里失败了，将转账明细转为人工确认
            GamePlatformTransferDetailRepository::setWaitManualConfirm($detail, $e->getMessage());
        }

        if ($transaction) {
            dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
        }

        return $detail;
    }

    /**
     * 转账后统一返回信息
     *
     * @param GamePlatformTransferDetail $detail
     * @return GamePlatformTransferDetail
     */
    public function transferResponse(GamePlatformTransferDetail $detail)
    {
        if ($detail->isSuccess()) {
            return $detail;
        } elseif ($detail->isFail()) {
            error_response(422, __('gamePlatform.TRANSFER_FAIL'));
        } else {
            error_response(422, __('gamePlatform.TRANSFER_PENDING'));
        }
    }

    /**
     * 子转子
     *
     * @param   GamePlatform    $fromGamePlatform   转出钱包
     * @param   GamePlatform    $toGamePlatform     转入钱包
     * @param   User            $user               会员
     * @param   float           $amount             转账金额
     * @param   string          $ip
     * @param   string          $adminName
     * @return  array
     */
    public function internalTransfer(GamePlatform $fromGamePlatform, GamePlatform $toGamePlatform, User $user, $amount, $ip = '', $adminName='')
    {
        $fromDetail = $this->transferOut($fromGamePlatform, $user, $amount, $ip, $adminName, true);

        $toDetail = $this->transferIn($toGamePlatform, $user->refresh(), $amount, $ip, null, $adminName);

        return [
            'from' => $fromDetail,
            'to'   => $toDetail,
        ];
    }

    /**
     * 不过主钱包转入
     *
     * @param   GamePlatform    $platform       平台
     * @param   User            $user           会员
     * @param   float           $amount         金额
     * @param   string          $type           类型  Adjustment
     * @param   boolean         $isIncome       是否是转入到第三方
     * @param   string          $ip
     * @return  GamePlatformTransferDetail
     */
    public function redirectTransfer(GamePlatform $platform, User $user, $amount, $type, $isIncome=false, $ip = '')
    {
        $amount = format_number(abs($amount), 2);

        $from = $isIncome ? $type : $platform->code;
        $to   = $isIncome ? $platform->code : $type;

        # 第三方游戏转账明细
        $detail = $this->addTransferDetail(
            $user,
            $platform,
            $from,
            $to,
            $isIncome,
            $amount,
            $ip
        );

        try {
            # 第三方转入
            $detail = $this->transfer($user, $platform, ['detail' => $detail]);
        } catch (\Exception $e) {
            GamePlatformTransferDetailRepository::setWaitingAndAddCheckJob($detail);
        }

        return $detail;
    }

    /**
     * 创建第三方转账明细
     *
     * @param User $user
     * @param GamePlatform $platform
     * @param string $from 转出钱包
     * @param string $to 转入钱包
     * @param integer $isIncome 转账类型:1、平台转入第三方， 2、第三方转入平台
     * @param float $amount 转张金额
     * @param string $userIp 会员ip
     * @param int $fromBeforeBalance 出账前钱包金额
     * @param int $toBeforeBalance 入账前钱包金额
     * @param null $userBonusPrizeId 红利奖励id
     * @param float $bonusAmount 红利奖励
     * @param string $adminName 管理员名称
     * @return  GamePlatformTransferDetail
     */
    public function addTransferDetail(
        User $user,
        GamePlatform $platform,
        $from,
        $to,
        $isIncome,
        $amount,
        $userIp,
        $fromBeforeBalance = 0,
        $toBeforeBalance = 0,
        $userBonusPrizeId = null,
        $bonusAmount = 0.0,
        $adminName=''
    )
    {
        # 转换汇率
        if ($rate = ExchangeRate::findRateByUserAndPlatform($user, $platform)) {
            $userCurrency     = $user->currency;
            $platformCurrency = $rate->platform_currency;
            $conversionAmount = round($amount * $rate->conversion_value, 2);
        } else {
            $userCurrency     = $user->currency;
            $platformCurrency = $user->currency;
            $conversionAmount = $amount;
        }

        return GamePlatformTransferDetailRepository::create(
            $user,
            $platform->code,
            $from,
            $to,
            $isIncome,
            $amount,
            $conversionAmount,
            $userCurrency,
            $platformCurrency,
            $userIp,
            $fromBeforeBalance,
            $toBeforeBalance,
            $userBonusPrizeId,
            $bonusAmount,
            $adminName
        );
    }

    public function checkTransferAmountLimit($currency, $amount)
    {
        $currency    = Currency::findByCodeFromCache($currency);
        $minTransfer = $currency->min_transfer;
        $maxTransfer = $currency->max_transfer;
        if ($amount < $minTransfer) {
            $showMinTransfer = thousands_number($minTransfer);
            error_response(422, __('gamePlatform.TRANSFER_AMOUNT_LESS_THAN_TRANSFER_MIN_LIMIT', ['amount' => $showMinTransfer . $currency->code]));
        }
        if ($amount > $maxTransfer){
            $showMaxTransfer = thousands_number($maxTransfer);
            error_response(422, __('gamePlatform.TRANSFER_AMOUNT_MORE_THAN_TRANSFER_MAX_LIMIT', ['amount' => $showMaxTransfer . $currency->code]));
        }
        return true;
    }

    /**
     * 判断转账平台钱包是否处于维护状态
     *
     * @param GamePlatform|null $fromPlatform
     * @param GamePlatform|null $toPlatform
     */
    public function checkPlatformIsWalletMaintain($fromPlatform = null, $toPlatform = null)
    {
        if ($fromPlatform && $fromPlatform->is_wallet_maintain){
            error_response(422, __('gamePlatform.platform_wallet_is_maintain', ['platform' => $fromPlatform->name]));
        }
        if ($toPlatform && $toPlatform->is_wallet_maintain){
            error_response(422, __('gamePlatform.platform_wallet_is_maintain', ['platform' => $toPlatform->name]));
        }
    }

    /**
     * 更新第三方odds
     *
     * @param User $user
     */
    public function updatePlatformUserOdds(User $user)
    {
        $platforms = GamePlatform::getAll()->where('status', true)->where('is_update_odds', true);
        foreach ($platforms as $platform) {
            $this->updateOdds($user, $platform);
        }
    }

    /**
     * 通过第三方获取游戏平台会员
     *
     * @param User $user
     * @param $platformCode
     * @param $isRemote
     * @return GamePlatformUser|mixed
     */
    public function getGamePlatformUserByUser(User $user, $platformCode, $isRemote=true)
    {
        if (UserAccount::isMainWallet($platformCode)) {
            $gamePlatformUser = new GamePlatformUser([
                'id'                    => null,
                'platform_code'         => $platformCode,
                'currency'              => $user->currency,
                'user_id'               => $user->id,
                'balance'               => $user->account->getAvailableBalance(),
                'platform_created_at'   => now(),
                'balance_status'        => true,
                'status'                => true,
            ]);
        } else {
            if (!$platform = GamePlatform::findByCode($platformCode)) {
                error_response(404, 'Not Found');
            }

            if ($isRemote) {
                try {
                    $gamePlatformUser = $this->balance($user, $platform);
                } catch (\Exception $e) {
                    $gamePlatformUser = GamePlatformUserRepository::findByUserAndPlatform($user->id, $platform->code);
                }
            } else {
                $gamePlatformUser = GamePlatformUserRepository::findByUserAndPlatform($user->id, $platform->code);
            }
        }

        return $gamePlatformUser;
    }
}
