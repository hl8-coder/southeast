<?php
namespace App\MatchRules;

use App\Models\Deposit;

class SACOMMatchRule extends BaseMatchRule {

    # 1、会员银行卡号 持卡人姓名 金额
    # 1、持卡人姓名 金额
    public function getTransactionQuery(Deposit $deposit, $query)
    {
        return $query->where(function($innerQuery) use ($deposit) {
            $innerQuery->where('description', 'like', "%$deposit->user_bank_account_no%")
                            ->where('description', 'like', "%$deposit->user_bank_account_name%")
                            ->where('credit', $deposit->arrival_amount);
                    })
                    ->orWhere(function($innerQuery) use ($deposit) {
                        $innerQuery->where('description', 'like', "%$deposit->user_bank_account_name%")
                            ->where('credit', $deposit->arrival_amount);
                    });
    }

}