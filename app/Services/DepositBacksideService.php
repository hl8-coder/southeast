<?php

namespace App\Services;

use App\Models\Adjustment;
use App\Models\Admin;
use App\Models\Deposit;
use App\Models\DepositLog;
use App\Models\Transaction;
use App\Models\Remark;
use App\Models\BankTransaction;
use App\Models\PaymentPlatform;
use App\Models\CompanyBankAccount;
use App\Models\CompanyBankAccountTransaction;
use App\Models\TurnoverRequirement;
use App\Models\User;
use App\Services\TransactionService;
use App\Repositories\CompanyBankAccountTransactionRepository;
use Carbon\Carbon;

class DepositBacksideService
{
    /**
     * Deposit Flow
     * 1 FO ticket create - Pending
     *    ------- Payment -------
     *  ✔ 1.1 Approve - Successful (CLOSE) if Amount >= 5000 go 1.1.1
     *       rule 1 required statement id
     *       rule 2 receive amount and credit amount is same , Unless have bank fee
     *       ------- Payment -------
     *     ✔ 1.1.1 Approve Change - Successful (CLOSE)
     *     ✔ 1.1.2 Cancel (go back 1) - Pending
     *  ✔ 1.2 Hold - Hold
     *       ------- Payment ------
     *     ✔ 1.2.1 Release Hold (go back 1) - Pending
     *       ---- HD:4 Bank Offline or HD:6 or Bank Lock ---
     *       ----- CS ----
     *     ✔ 1.2.1 Release Hold (go back 1) - Pending
     *     ✔ 1.2.2 Request Advance
     *         ✔ 1.2.2.1 Approve Adv
     *               ------- CS ------
     *             ✔ 1.2.2.1.1 Revert Action (go back 1.2) - Hold
     *               ------- Payment ------
     *             ✔ 1.2.2.1.1 Revert Action (go back 1.2) - Hold
     *             ✔ 1.2.2.1.2 Approve Advance Credit - Successful (OPEN)
     *                  rule 1 receive amount and credit amount is same , Unless have bank fee
     *                          new remark
     *                              type: hold category
     *                              category: Advance Credit
     *                              Resaon: advance credit transaciton ID: 1, amount: 500
     *                ✔ 1.2.2.1.2.1 Match - Successful (CLOSED)
     *                          update remark
     *                              REMOVE REASON: advance credit transaciton ID: 1, status: closed
     *                          release hold
     *         ✔ 1.2.2.2 Approve Partial
     *               ------- CS ------
     *             ✔ 1.2.2.2.1 Revert Action (go back 1.2) - Hold
     *               ------- Payment ------
     *             ✔ 1.2.2.2.1 Revert Action (go back 1.2) - Hold
     *             ✔ 1.2.2.2.2 Approve Partial Advance Credit - Successful (OPEN)
     *                          new adjustment
     *                                  category deposit
     *                                  remark : advance credit transaciton ID: 1, parial amount: 500
     *                          new remark
     *                              type: hold category
     *                              category: Advance Credit
     *                              Resaon: advance credit transaciton ID: 1, parial amount: 500
     *                          -------
     *                ✔ 1.2.2.2.2.1 Match - Successful (CLOSE)
     *                          update remark
     *                              REMOVE REASON: advance credit transaciton ID: 1, status: closed
     *                          release hold
     *         ✔ 1.2.2.3 Reject - Failed (CLOSE)
     *         ✔ 1.2.2.4 Revert Action (go back 1.2) - Hold
     *  ✔ 1.3 Reject - Failed (done) if Amount >= 5000 go 1.1.1
     *       ------- Payment -------
     *     ✔ 1.3.1 Approve Change - Failed (CLOSE)
     *     ✔ 1.3.2 Canel (go back 1) - Pending
     *  ✔ 1.4 Match - Successful (CLOSE)
     *      trigger auto approve
     *
     * 2 CallBack Success - Successful (CLOSE)
     * 3 CallBack Fail - Failed (CLOSE) only Card
     * 4 Lose - Successful (LOSE)
     */

    /**
     * 直接批准
     *
     */
    public function approve($user, $deposit)
    {
        # rule if remark not remove show
        $this->ruleCheckNoRemoveRemarks($deposit);

        # rule 1 required statement id
        # 只有银行卡需要
        if ($deposit->payment_type == PaymentPlatform::PAYMENT_TYPE_BANKCARD) {
            $this->ruleCheckStatementId($deposit);
        }

        # rule 2 receive amount and credit amount is same , Unless have bank fee
        $this->ruleCheckAmount($deposit);

        # 帐变
        $transactions = [];

        # 更新的资料
        $data["status"]            = Deposit::STATUS_RECHARGE_SUCCESS;
        $data["is_advance_credit"] = true;
        $data["button_flow_code"]  = "1.1";

        # 二次批准
        if ($this->checkNeedSecondAction($deposit)) {
            $data["need_second_approve"] = true;
            DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_APPROVE);
        } else {
            $data["tag"] = Deposit::TAG_CLOSED;
            # 上分
            $transactions = $this->credit($user, $deposit);
            DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_SUCCESS);
        }

        # 更新
        if (!$deposit->update($data)) {
            error_response(422, 'Status error.');
        }

        return $transactions;
    }

    /**
     * 拒绝
     *
     */
    public function reject($user, $deposit)
    {
        if ($deposit->statement_id) {
            error_response(422, 'This ticket has statement id , can\'t reject.');
        }

        # 更新的资料
        $data["status"]            = Deposit::STATUS_RECHARGE_FAIL;
        $data["is_advance_credit"] = true;

        switch ($deposit->button_flow_code) {
            # 直拒绝
            case '1':
                $data["button_flow_code"] = "1.3";
                # 二次拒绝
                if (!$this->checkNeedSecondAction($deposit)) {
                    $data["need_second_approve"] = false;
                    $data["tag"]                 = Deposit::TAG_CLOSED;
                } else {
                    $data["need_second_approve"] = true;
                }

                break;
            # 客服拒绝
            case '1.2.2':
                $data["need_second_approve"] = false;
                $data["button_flow_code"]    = "1.2.2.3";
                $data["tag"]                 = Deposit::TAG_CLOSED;
                break;
        }

        # 更新
        if (!$deposit->update($data)) {
            error_response(422, 'Status error.');
        }

        # 写入Log
        $reason = isset(Deposit::$rejectReasons[$deposit->reject_reason]) ? Deposit::$rejectReasons[$deposit->reject_reason] : null;
        DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_REJECT, null, $reason);
    }

    /**
     * 取消
     *
     */
    public function cancel($user, $deposit)
    {
        if ($deposit->bankTransaction) {
            $deposit->bankTransaction->update([
                'transaction_id' => '',
                'deposit_id'     => null,
                'status'         => BankTransaction::STATUS_NOT_MATCH,
            ]);
        }


        # 更新的资料
        $data = $this->getPendingData();

        # 更新
        if (!$deposit->update($data)) {
            error_response(422, 'Status error.');
        }

        # 写入Log
        DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_CANCEL);
    }


    /**
     * 保留
     *
     */
    public function hold($user, $deposit)
    {
        # 更新的资料
        $data["status"]            = Deposit::STATUS_HOLD;
        $data["is_advance_credit"] = $deposit->IsAdvanceCreditHoldReason($deposit->hold_reason);
        $data["button_flow_code"]  = "1.2";
        $data['hold_reason']       = $deposit->hold_reason;

        # 更新
        if (!$deposit->where('id', $deposit->id)->where('button_flow_code', '1')->update($data)) {
            error_response(422, 'Status error.');
        }
        $reason = isset(Deposit::$holdReasons[$deposit->hold_reason]) ? Deposit::$holdReasons[$deposit->hold_reason] : null;

        # 写入Log
        DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_HOLD, null, $reason);
    }

    /**
     * 取消保留
     *
     */
    public function releaseHold($user, $deposit)
    {
        # 更新的资料
        $data = $this->getPendingData();

        # 更新
        if (!$deposit->update($data)) {
            error_response(422, 'Status error.');
        }

        # 写入Log
        DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_RELEASE_HOLD);
    }

    /**
     * 上分类型选择
     *
     */
    public function requestAdvance($user, $deposit)
    {
        $data["button_flow_code"] = "1.2.2";

        # 更新
        if (!$deposit->update($data)) {
            error_response(422, 'Status error.');
        }

        # 写入Log
        DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_REQUEST_ADVANCE);
    }

    /**
     * 二次批准或拒绝
     *
     */
    public function approveChanges($user, $deposit)
    {
        # 帐变
        $transactions = [];

        # 更新的资料
        $data["tag"]                 = Deposit::TAG_CLOSED;
        $data["need_second_approve"] = false;

        switch ($deposit->button_flow_code) {
            # 批准
            case '1.1':
                $data["button_flow_code"] = "1.1.1";
                $transactions             = $this->credit($user, $deposit);
                DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_SUCCESS);
                break;
            # 拒绝
            case '1.3':
                $data["button_flow_code"] = "1.3.1";
                DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_REJECT);
                break;
        }

        # 更新
        if (!$deposit->update($data)) {
            error_response(422, 'Status error.');
        }

        return $transactions;
    }

    /**
     * 请求全额上分
     *
     */
    public function approveAdv($user, $deposit)
    {
        $data["button_flow_code"] = "1.2.2.1";

        # 更新
        if (!$deposit->update($data)) {
            error_response(422, 'Status error.');
        }

        # 写入Log
        DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_APPROVE_ADV);
    }

    /**
     * 请求部份上分
     *
     */
    public function approvePartial($user, $deposit)
    {
        # 部份上分金額不能比充值金額大
        if ($deposit->partial_amount > $deposit->arrival_amount) {
            $error = "partial_amout error.";
            error_response(422, 'Status error.');
        }

        $this->ruleCheckCreditHistory($deposit);

        $data["button_flow_code"] = "1.2.2.2";
        $data["is_partial"]       = true;
        $data["remarks"]          = "Request Partial Amount:" . thousands_number($deposit->partial_amount);

        # 更新
        if (!$deposit->update($data)) {
            error_response(422, 'Status error.');
        }

        # 写入Log
        DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_APPROVE_PARTIAL);
    }


    /**
     * 取消请求
     *
     */
    public function revertAction($user, $deposit)
    {
        $data["button_flow_code"] = "1.2";
        $data["is_partial"]       = false;
        $data["partial_amount"]   = 0;

        # 更新
        if (!$deposit->update($data)) {
            error_response(422, 'Status error.');
        }

        # 写入Log
        DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_REVERT_ACTION);
    }


    /**
     * 全额上分
     *
     */
    public function approveAdvanceCredit($user, $deposit)
    {
        # rule 1 receive amount and credit amount is same , Unless have bank fee
        $this->ruleCheckAmount($deposit);

        $this->ruleCheckCreditHistory($deposit);

        # 帐变
        $transactions = [];

        # 更新的资料
        $data["status"]           = Deposit::STATUS_RECHARGE_SUCCESS;
        $data["button_flow_code"] = "1.2.2.1.2";
        $transactions             = $this->credit($user, $deposit);

        # 新增remark
        $remark = $this->addRemark($user, $deposit);

        # 用来解除remark用
        $data["partial_remark_id"] = $remark->id;

        # 更新
        if (!$deposit->update($data)) {
            error_response(422, 'Status error.');
        }

        # 写入Log
        DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_SUCCESS);

        return $transactions;
    }

    /**
     * 部份上分
     *
     */
    public function approvePartialAdvanceCredit($user, $deposit)
    {
        # rule 1 receive amount and credit amount is same , Unless have bank fee
        $this->ruleCheckAmount($deposit);

        # 帐变
        $transactions = [];

        # 更新的资料
        $data["status"]           = Deposit::STATUS_RECHARGE_SUCCESS;
        $data["button_flow_code"] = "1.2.2.2.2";
        $transactions             = $this->partialAdjustment($user, $deposit);

        # 更新
        if (!$deposit->update($data)) {
            error_response(422, 'Status error.');
        }

        # 写入Log
        DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_SUCCESS);

        return $transactions;
    }

    /**
     * 充值领取银行交易记录
     *
     */
    public function match($user, $deposit, $bankTransaction)
    {
        if (!is_null($bankTransaction->deposit_id)) {
            error_response(422, 'Transaction history has been received.');
        }

        if ($deposit->fund_in_account != $bankTransaction->fund_in_account) {
            error_response(422, 'The Fund in Account is different.');
        }

//        if ($deposit->amount != $deposit->arrival_amount + $deposit->bank_fee) {
//            error_response(422, 'The amount of transaction is different.');
//        }

        if ($deposit->arrival_amount != $bankTransaction->credit) {
            error_response(422, 'The amount of transaction is different.');
        }

        $bankTransaction->update([
            'transaction_id' => $deposit->order_no,
            'deposit_id'     => $deposit->id,
            'status'         => BankTransaction::STATUS_MATCH,
        ]);

        # 帐变
        $transactions = [];

        # 更新的资料
        $data["statement_id"] = $bankTransaction->id;
        $data["statement_at"] = Carbon::now();

        if (!$deposit->isClosed()) {
            switch ($deposit->button_flow_code) {
                case '1.2.2.1.2':
                    $data["button_flow_code"] = "1.2.2.1.2.1";
                    $data["tag"]              = Deposit::TAG_CLOSED;
                    $data["hold_reason"]      = 0;
                    $this->releaseRemark($user, $deposit);
                    break;
                # 部份上分
                case '1.2.2.2.2':
                    $data["button_flow_code"] = "1.2.2.2.2.1";
                    $data["tag"]              = Deposit::TAG_CLOSED;
                    $data["hold_reason"]      = 0;
                    $data["remarks"]          = "";
                    $data["arrival_amount"]   = $deposit->arrival_amount - $deposit->partial_amount;
                    $transactions             = $this->credit($user, $deposit);
                    break;
                default:
                    # rule if remark not remove show
                    $this->ruleCheckNoRemoveRemarks($deposit);

                    if ($this->checkNeedSecondAction($deposit)) {
                        $data["hold_reason"]         = 0;
                        $data["need_second_approve"] = true;
                        $data["status"]              = Deposit::STATUS_RECHARGE_SUCCESS;
                        $data["is_advance_credit"]   = true;
                        $data["button_flow_code"]    = "1.1";
                    } else {
                        $data["need_second_approve"] = false;
                        $data["hold_reason"]         = 0;
                        $data["tag"]                 = Deposit::TAG_CLOSED;
                        $data["status"]              = Deposit::STATUS_RECHARGE_SUCCESS;
                        $data["is_advance_credit"]   = true;
                        $data["button_flow_code"]    = "1.4";

                        $transactions = $this->credit($user, $deposit);
                    }
                    break;
            }

        }

        # 更新
        if (!$deposit->update($data)) {
            error_response(422, 'Status error.');
        }

        DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_MATCH, $bankTransaction->id);

        return $transactions;
    }

    /**
     * 取消充值领取银行交易记录
     *
     */
    public function unmatch($user, $deposit)
    {
        if (!$deposit->bankTransaction) {
            error_response(422, 'No transaction.');
        }

        $deposit->bankTransaction->update([
            'transaction_id' => '',
            'deposit_id'     => null,
            'status'         => BankTransaction::STATUS_NOT_MATCH,
        ]);

        $deposit->update([
            'statement_id' => null,
        ]);

        DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_UNMATCH, $deposit->bankTransaction->id);
    }

    /**
     * 在未获得对账信息前，出于对会员信任，先部分上分后，再全额上分
     * zpay, mpay, linepay部分上分使用
     */
    public function finalApprove(Admin $user, Deposit $deposit)
    {
        # 帐变
        $transactions = [];

        if (!$deposit->isClosed()) {
            # 更新的资料
            $data["statement_at"] = Carbon::now();
            switch ($deposit->button_flow_code) {
                case '1.2.2.1.2':
                    $data["button_flow_code"] = "1.2.2.1.2.1";
                    $data["tag"]              = Deposit::TAG_CLOSED;
                    $data["hold_reason"]      = 0;
                    $this->releaseRemark($user, $deposit);
                    break;
                # 部份上分
                case '1.2.2.2.2':
                    $data["button_flow_code"] = "1.2.2.2.2.1";
                    $data["tag"]              = Deposit::TAG_CLOSED;
                    $data["hold_reason"]      = 0;
                    $data["remarks"]          = "";
                    $data["arrival_amount"]   = $deposit->arrival_amount - $deposit->partial_amount;
                    $transactions             = $this->credit($user, $deposit);
                    break;
            }
            $deposit->update($data);
        } else {
            error_response(422, 'Status error.');
        }

        DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_FINAL_APPROVE);

        return $transactions;
    }


    /**
     * 上分
     *
     */
    public function credit($user, $deposit)
    {
        $transactions = [];

        $amount = $deposit->arrival_amount;
        # 创建流水要求
        TurnoverRequirement::add($deposit, $deposit->is_turnover_closed);

        if ($deposit->is_partial) {
            $amount = $deposit->arrival_amount - $deposit->partial_amount;
            $this->releaseRemark($user, $deposit);
        } else {
            # rule if remark not remove show
            $this->ruleCheckNoRemoveRemarks($deposit);
        }

        # 银行卡
        $transactions[] = (new TransactionService())->addTransaction(
            $deposit->user,
            $amount,
            Transaction::TYPE_ONLINE_BANKING_SAVE,
            $deposit->id,
            $deposit->order_no
        );

        # 送点
        if ((float)$deposit->reimbursement_fee > 0) {
            $message                    = sprintf("Approve Transaction ID: %s, Reimbursement Fee: %s", $deposit->order_no, thousands_number($deposit->reimbursement_fee));
            $data["type"]               = Adjustment::TYPE_DEPOSIT;
            $data["category"]           = Adjustment::CATEGORY_REIMBURSEMENT;
            $data['amount']             = $deposit->reimbursement_fee;
            $data["reason"]             = $message;
            $data["remark"]             = $message;
            $data['user_id']            = $deposit->user_id;
            $data['user_name']          = $deposit->user->name;
            $data['created_admin_name'] = $user->name;

            $adjustment = Adjustment::create($data);
            $adjustment->approve($user->name);

            $transactions[] = (new TransactionService())->addTransaction(
                $adjustment->user,
                $adjustment->amount,
                Transaction::TYPE_ADJUSTMENT_IN,
                $adjustment->id,
                $adjustment->order_no
            );
        }

        if ($deposit->payment_type == PaymentPlatform::PAYMENT_TYPE_BANKCARD) {
            # 添加公司银行卡交易记录
            $companyBankAccount = CompanyBankAccount::findByCode($deposit->fund_in_account);
            CompanyBankAccountTransactionRepository::add(
                $companyBankAccount,
                CompanyBankAccountTransaction::TYPE_DEPOSIT,
                true,
                $deposit->arrival_amount,
                $deposit->bank_fee,
                $user->name,
                $deposit->user->name,
                $deposit->id,
                $deposit->user_bank_account_no ? $deposit->user_bank_account_no : $deposit->reference_id,
                $deposit->fund_in_account,
                $deposit->remarks,
                null,
                $deposit->order_no
            );
        }

        # 暂时先放这里更新上分时间
        $deposit->update(['approved_at' => now()]);

        return $transactions;
    }

    /**
     * 部份调整上分
     *
     */
    public function partialAdjustment($user, &$deposit)
    {
        $transactions = [];

        $message = sprintf("Advance Credit Transaction ID: %s, Parial Amount: %s ", $deposit->order_no, thousands_number($deposit->partial_amount));

        # 新增adjustment
        $data["type"]               = Adjustment::TYPE_DEPOSIT;
        $data["category"]           = Adjustment::CATEGORY_DEPOSIT;
        $data['amount']             = $deposit->partial_amount;
        $data["reason"]             = $message;
        $data["remark"]             = $message;
        $data['user_id']            = $deposit->user_id;
        $data['user_name']          = $deposit->user->name;
        $data['created_admin_name'] = $user->name;

        $adjustment = Adjustment::create($data);
        $adjustment->approve($user->name);

        $transactions[] = (new TransactionService())->addTransaction(
            $adjustment->user,
            $adjustment->amount,
            Transaction::TYPE_ADJUSTMENT_IN,
            $adjustment->id,
            $adjustment->order_no
        );

        unset($data);

        # 新增remark
        $remark = $this->addRemark($user, $deposit, $message);

        # 用来解除remark用
        $deposit->partial_remark_id = $remark->id;

        return $transactions;
    }

    /**
     * 新增remark
     *
     */
    public function addRemark($user, $deposit, $message = null)
    {
        if (!$deposit->is_partial) {
            $message = sprintf("Advance Credit Transaction ID: %s, Amount: %s ", $deposit->order_no, thousands_number($deposit->arrival_amount));
        }

        $data['user_id']    = $deposit->user_id;
        $data['type']       = Remark::TYPE_HOLD_WITHDRAWAL_AND_DEPOSIT;
        $data['category']   = Remark::CATEGORY_ADVANCE_CREDIT;
        $data['reason']     = $message;
        $data['admin_name'] = $user->name;
        $remark             = Remark::create($data);

        return $remark;
    }

    /**
     * 解除remark
     *
     * @param $user
     * @param $deposit
     * @param string $message
     */
    public function releaseRemark($user, $deposit, $message = '')
    {
        if (!$message) {
            $message = sprintf("Advance Credit Transaction ID: %s, Status: Closed ", $deposit->order_no);
        }

        $remark                    = Remark::withTrashed()->find($deposit->partial_remark_id);
        $remark->remove_reason     = $message;
        $remark->remove_admin_name = $user->name;
        $remark->deleted_at        = now();
        $remark->save();
    }

    /**
     * 更新金额细节
     *
     */
    public function updateAmountDetail($user, $deposit)
    {
        # rule 1 receive amount and credit amount is same , Unless have bank fee
        $this->ruleCheckAmount($deposit);

        # rule if remark not remove show error
        $this->ruleCheckNoRemoveRemarks($deposit);

        # 更新
        if (!$deposit->save()) {
            error_response(422, 'Status error.');
        }

        # 写入Log
        DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_AMOUNT_DETAIL);
    }

    /**
     * 订单遗失
     *
     */
    public function lose($user, $deposit, $release_remark)
    {
        if (!($deposit->isOpen() && $deposit->isCompleted())) {
            error_response(422, 'Status error.');
        }

        $this->releaseRemark($user, $deposit, $release_remark);

        $data["button_flow_code"] = "4";
        $data["tag"]              = Deposit::TAG_LOSE;
        $data["hold_reason"]      = 0;

        # 更新
        if (!$deposit->update($data)) {
            error_response(422, 'Status error.');
        }

        # 写入Log
        DepositLog::add($user->name, $deposit->id, DepositLog::TYPE_AMOUNT_DETAIL);

    }


    /**
     * 取得预设资料
     *
     */
    private function getPendingData()
    {
        $data["need_second_approve"] = false;
        $data["statement_id"]        = null;
        $data["status"]              = Deposit::STATUS_CREATED;
        $data["is_advance_credit"]   = false;
        $data["button_flow_code"]    = "1";
        $data["tag"]                 = Deposit::TAG_OPEN;
        $data["hold_reason"]         = 0;
        $data["reject_reason"]       = 0;
        $data["is_partial"]          = 0;
        $data["partial_amount"]      = 0;
        $data["remarks"]             = "";

        return $data;
    }

    private function ruleCheckCreditHistory($deposit)
    {
        if ($deposit->user->remarks->where("type", Remark::TYPE_HOLD_DEPOSIT)
                ->where("category", Remark::CATEGORY_ADVANCE_CREDIT)
                ->where("remove_reason", "")->count() > 0
        ) {
            error_response(422, 'This ticket can\'t approve credit.');
        }
    }

    /**
     * receive amount and credit amount is same , Unless have bank fee
     *
     */
    private function ruleCheckAmount($deposit)
    {
        if ($deposit->reimbursement_fee > 50) {
            error_response(422, 'This ticket receive amount error.');
        }

        if ($deposit->bank_fee > 50) {
            error_response(422, 'This ticket bank amount error.');
        }

        if ($deposit->bank_fee > 0 && $deposit->reimbursement_fee > 0) {
            error_response(422, 'This ticket amount error.');
        }
    }

    /**
     * required statement id
     *
     */
    private function ruleCheckStatementId($deposit)
    {
        if (!$deposit->statement_id) {
            error_response(422, 'This ticket need statement id.');
        }
    }

    public function ruleCheckNoRemoveRemarks($deposit)
    {
        $count = $deposit->user->remarks->whereIn("type", Remark::$holdDepositTypes)->where("remove_reason", "")->count();

        if ($count) {
            error_response(422, 'Remarks are not removed and cannot be updated.');
        }
    }

    private function checkNeedSecondAction($deposit)
    {
        // now for TH
        return $deposit->arrival_amount >= 10000 && $deposit->payment_type == PaymentPlatform::PAYMENT_TYPE_BANKCARD;
    }
}
