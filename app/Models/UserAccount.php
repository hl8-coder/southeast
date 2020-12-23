<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserAccount extends Model
{
    protected $guarded = [];

    protected $casts = [
        'total_balance'       => 'float',
        'freeze_balance'      => 'float',
        'total_point_balance' => 'float',
    ];

    const MAIN_WALLET = 'Main_Wallet';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    # 方法 start

    /**
     * 是否是主钱包
     *
     * @param $wallet
     * @return bool
     */
    public static function isMainWallet($wallet)
    {
        return static::MAIN_WALLET == $wallet;
    }

    /**
     * 判断金额是否足够
     *
     * @param $amount
     * @return bool
     */
    public function isBalanceEnough($amount)
    {
        return $this->getAvailableBalance() >= $amount;
    }

    public function getAvailableBalance()
    {
        return $this->total_balance - $this->freeze_balance;
    }

    public static function getLangName()
    {
        return __('dropList.MAIN_WALLET');
    }


    /**
     * 账户金额加操作
     *
     * @param  UserAccount  $userAccount
     * @param  float        $amount         变动金额
     * @throws \Exception
     * @return UserAccount
     */
    public static function addTotalBalance(UserAccount $userAccount, $amount)
    {
        $amount = abs($amount);
        $affectRow = static::query()->where('user_id', $userAccount->user_id)->increment('total_balance', $amount);

        if (1 != $affectRow) {
            Log::info('add balance fail:' . $affectRow);
            Log::info('name: ' . $userAccount->user->name . ', 追踪信息: 75');
            throw new \Exception(__('userAccount.BALANCE_NOT_ENOUGH'));
        }

        return $userAccount->refresh();
    }

    /**
     * 账户金额减操作
     *
     * @param  UserAccount  $userAccount
     * @param  float        $amount         变动金额
     * @throws \Exception
     * @return UserAccount
     */
    public static function delTotalBalance(UserAccount $userAccount, $amount)
    {
        $amount = abs($amount);
        $affectRow = static::query()->where('user_id', $userAccount->user_id)
            ->where('total_balance', $userAccount->total_balance)
            ->whereRaw(DB::raw("total_balance - freeze_balance - $amount >= 0"))
            ->decrement('total_balance', $amount);

        if (1 != $affectRow) {
            Log::info('name: ' . $userAccount->user->name . ', 追踪信息: 99');
            throw new \Exception(__('userAccount.BALANCE_NOT_ENOUGH'));
        }

        return $userAccount->refresh();
    }

    /**
     * 冻结金额
     *
     * @param   UserAccount     $userAccount
     * @param   float           $amount         冻结金额
     * @throws
     */
    public static function freeze(UserAccount $userAccount, $amount)
    {
        $amount = abs($amount);
        $affectRow = static::query()->where('user_id', $userAccount->user_id)
            ->whereRaw(DB::raw("total_balance - freeze_balance - $amount >= 0"))
            ->increment('freeze_balance', $amount);

        if (1 != $affectRow) {
            Log::info('name: ' . $userAccount->user->name . ', 追踪信息: 121');
            throw new \Exception(__('userAccount.BALANCE_NOT_ENOUGH'));
        }
    }

    /**
     * 解冻金额
     *
     * @param   UserAccount       $userAccount
     * @param   float             $amount           解冻金额
     * @throws
     */
    public static function unfreeze(UserAccount $userAccount, $amount)
    {
        $amount = abs($amount);
        $affectRow = static::query()->where('user_id', $userAccount->user_id)
            ->whereRaw(DB::raw("freeze_balance - $amount >= 0"))
            ->decrement('freeze_balance', $amount);

        if (1 != $affectRow) {
            Log::info('name: ' . $userAccount->user->name . ', 追踪信息: 142');
            throw new \Exception(__('userAccount.BALANCE_NOT_ENOUGH'));
        }
    }
    # 方法 end
}
