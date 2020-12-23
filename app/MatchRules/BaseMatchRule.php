<?php
namespace App\MatchRules;

use App\Models\Deposit;

class BaseMatchRule {

    public function getTransactionQuery(Deposit $deposit, $query)
    {
        return;
    }
}