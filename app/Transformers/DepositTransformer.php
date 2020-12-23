<?php
namespace App\Transformers;

use App\Models\Admin;
use App\Models\Deposit;
use App\Models\User;
use App\Models\PaymentPlatform;
use App\Payments\Doicard5s;
use Carbon\Carbon;

/**
 * @OA\Schema(
 *   schema="Deposit",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="充值id"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="user_ip", type="string", description="提现ip"),
 *   @OA\Property(property="order_no", type="string", description="交易订单号"),
 *   @OA\Property(property="payment_type", type="integer", description="支付类型"),
 *   @OA\Property(property="payment_platform_id", type="integer", description="支付平台id"),
 *   @OA\Property(property="company_bank_code", type="string", description="公司卡银行辨识码"),
 *   @OA\Property(property="online_banking_channel", type="integer", description="公司卡支付渠道"),
 *   @OA\Property(property="fund_in_account", type="string", description="充值银行卡帐号"),
 *   @OA\Property(property="amount", type="number", description="提现金额"),
 *   @OA\Property(property="receive_amount", type="number", description="实际收款金额"),
 *   @OA\Property(property="arrival_amount", type="number", description="实际到账金额"),
 *   @OA\Property(property="bank_fee", type="number", description="手续费"),
 *   @OA\Property(property="reimbursement_fee", type="number", description="报销费(公司承担手续用)"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="tag", type="integer", description="标签"),
 *   @OA\Property(property="hold_reason", type="integer", description="保留原因"),
 *   @OA\Property(property="reject_reason", type="integer", description="拒绝原因"),
 *   @OA\Property(property="last_access", type="integer", description="最后存取管理员"),
 *   @OA\Property(property="last_upload", type="integer", description="经过时间(秒)"),
 *   @OA\Property(property="color", type="string", description="对应色码"),
 *   @OA\Property(property="user_bank_account_name", type="string", description="会员开户人姓名"),
 *   @OA\Property(property="user_bank_account_no", type="string", description="会员开户账号"),
 *   @OA\Property(property="reference_id", type="string", description="银行回应码"),
 *   @OA\Property(property="receipts", type="string", description="凭证图片id(,逗号分割)"),
 *   @OA\Property(property="remarks", type="string", description="备注"),
 *   @OA\Property(property="statement_id", type="string", description="银行对应ID"),
 *   @OA\Property(property="payment_platform_order_no", type="string", description="支付平台订单号"),
 *   @OA\Property(property="payment_bank_code", type="string", description="支付平台回应码"),
 *   @OA\Property(property="payment_reference", type="string", description="支付平台银行"),
 *   @OA\Property(property="user_mpay_number", type="string", description="会员Mpay帐号"),
 *   @OA\Property(property="mpay_trading_code", type="string", description="Mpay追踪码"),
 *   @OA\Property(property="deposit_at", type="string", description="充值时间", format="date-time"),
 *   @OA\Property(property="approved_at", type="string", description="支付时间", format="date-time"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 *   @OA\Property(property="need_second_approve", type="string", description="是否二次批准"),
 *   @OA\Property(property="button_flow_code", type="string", description="功能按钮流程代码"),
 *   @OA\Property(property="user", description="会员信息", ref="#/components/schemas/User"),
 *   @OA\Property(property="userDeposits", description="会员充值资料", ref="#/components/schemas/UserDeposit"),
 *   @OA\Property(property="userAccount", ref="#/components/schemas/UserAccount"),
 *   @OA\Property(property="userBank", description="会员银行信息", ref="#/components/schemas/Bank"),
 *   @OA\Property(property="logs", description="充值异动log", ref="#/components/schemas/DepositLog"),
 *   @OA\Property(property="paymentPlatform", description="支付平台", ref="#/components/schemas/PaymentPlatform"),
 *   @OA\Property(property="images", description="图片", ref="#/components/schemas/Image"),
 * )
 */
class DepositTransformer extends Transformer
{
    protected $availableIncludes = ['user', 'accessLogs', 'operationLogs', 'userBank', 'userAccount', 'userInfo', 'userDeposits', 'paymentPlatform', 'bankTransaction', 'images'];

    public function transform(Deposit $deposit)
    {
        # 轻过时间
        $almostSeconds = $deposit->created_at->diffInSeconds(Carbon::now());

        # 对应色码
        $color = "";

        if(!$deposit->isClosed()) {
            # VIP
            if((int)$deposit->user->vip_id > 1) {
                $color = "VIP";
            }
            # ADVANCE CREDIT
            else if($deposit->is_advance_credit && in_array($deposit->button_flow_code, ['1.2.2.1', '1.2.2.2'])) {
                $color = "ADVANCE CREDIT";
            }
            # ALMOST 15 MINS
            else if($almostSeconds > 900 && $deposit->status == Deposit::STATUS_CREATED) {
                $color = "ALMOST 15 MINS";
            }
            # ALMOST 5 MINS
            else if($almostSeconds > 300 && $deposit->status == Deposit::STATUS_CREATED) {
                $color = "ALMOST 5 MINS";
            }
            else if ($deposit->status == Deposit::STATUS_CREATED && $deposit->auto_status == Deposit::AUTO_STATUS_PROCESSING) {
                $color = "AUTO DEPOSIT";
            }
        }

        # 調整經過時間格式, 暂时保留
        $lastUpload = "";
//        if($deposit->statement_at) {
//            $statementSeconds = (new Carbon($deposit->statement_at))->diffInSeconds(Carbon::now());
//            $lastUpload = (int)($statementSeconds / 60). ":" . str_pad($statementSeconds % 60, 2 , '0', STR_PAD_LEFT);
//        }

        $lastLog = $deposit->logs()->latest()->first();

        $cardType = $deposit->card_type;
        if ($deposit->paymentPlatform && 'Paytrust88-quickpay' == $deposit->paymentPlatform->code) {
            $paytrustMapping = [
                '59f414091aeb1' => 'Kasikorn Bank',
                '59f4143921ba5' => 'Bangkok Bank',
                '59f414434c28e' => 'KTB NetBank',
                '59f414509ca5d' => 'SCB Easy',
            ];
            $deposit->payment_bank_code = isset($paytrustMapping[$deposit->payment_bank_code]) ? $paytrustMapping[$deposit->payment_bank_code] : '';
        } else if ($deposit->paymentPlatform && 'Doicard5s-card' == $deposit->paymentPlatform->code) {
            $cardTypeRow = collect(Doicard5s::$showCardTypes)->where('key', $deposit->card_type)->first();
            if (!empty($cardTypeRow)) {
                $cardType = $cardTypeRow['value'];
            }
        }

        if ($lastLog) {
            $admin = Admin::query()->where('name', $lastLog->admin_name)->first();
            if ($admin) {
                $lastAccess = $lastLog->admin_name;
            } else {
                $lastAccess = 'System Auto';
            }
        } else {
            $lastAccess = '';
        }

        return  [
            'id'                    => $deposit->id,
            'currency'              => $deposit->currency,
            'user_id'               => $deposit->user_id,
            'user_name'             => $deposit->user->name,
            'user_ip'               => $deposit->user_ip,
            'device'                => $deposit->device,
            'display_device'        => transfer_show_value($deposit->device, User::$devices),
            'order_no'              => $deposit->order_no,
            'payment_type'          => $deposit->payment_type,
            'payment_platform_id'   => $deposit->payment_platform_id,
            'company_bank_code'     => $deposit->fund_in_account ? $deposit->fund_in_account : $deposit->company_bank_code,
            'online_banking_channel'=> $deposit->online_banking_channel,
            'fund_in_account'       => $deposit->fund_in_account,

            'amount'                => thousands_number($deposit->amount),
            'receive_amount'        => thousands_number($deposit->receive_amount),
            'arrival_amount'        => thousands_number($deposit->arrival_amount),
            'bank_fee'              => thousands_number($deposit->bank_fee),
            'reimbursement_fee'     => thousands_number($deposit->reimbursement_fee),

            'partial_amount'        => thousands_number($deposit->partial_amount),

            'status'                => $deposit->status,
            'tag'                   => $deposit->tag,
            'hold_reason'           => $deposit->hold_reason,
            'reject_reason'         => $deposit->reject_reason,
            'last_access'           => $lastAccess,
            'last_upload'           => $deposit->statement_at,
            'color'                 => $color,

            //Online Banking DETAILS
            'user_bank_account_name'=> $deposit->user_bank_account_name,
            'user_bank_account_no'  => $deposit->user_bank_account_no,
            'reference_id'          => $deposit->reference_id,
            'receipts'              => $deposit->receipts,
            'internal_remarks'      => $deposit->remarks,
            'external_remarks'      => $deposit->remarks,
            'remarks'               => $deposit->remarks,
            'statement_id'          => $deposit->statement_id,

            //ThirdParty
            'payment_platform_order_no' => $deposit->payment_platform_order_no,
            'payment_bank_code'      => $deposit->payment_bank_code,
            'payment_reference'      => $deposit->payment_reference,

            //Mpay
            'mpay_trading_code'     => $deposit->mpay_trading_code,
            'user_mpay_number'      => $deposit->user_mpay_number,

            # card
            'card_type'         => $cardType,
            'pin_number'        => $deposit->pin_number,
            'serial_number'     => $deposit->serial_number,

            //Linepay
            'linepay_id'            => $deposit->linepay_id,

            //Button Display
            'btn_approve_show'        => $deposit->checkApprove(),
            'btn_hold_show'         => $deposit->checkHold(),
            'btn_reject_show'       => $deposit->checkReject(),
            'btn_cancel_show'       => $deposit->checkCancel(),
            'btn_approve_changes_show' => $deposit->checkApproveChanges(),
            'btn_request_advance_show' => $deposit->checkRequestAdvance(),
            'btn_release_hold_show' => $deposit->checkReleaseHold(),

            'btn_approve_adv_show'    => $deposit->checkApproveAdv(),
            'btn_approve_partial_show'=> $deposit->checkApprovePartial(),
            'btn_revert_action_show'  => $deposit->checkRevertAction(),
            'btn_approve_advance_credit_show'  => $deposit->checkApproveAdvanceCredit(),
            'btn_approve_partial_advance_credit_show'  => $deposit->checkApprovePartialAdvanceCredit(),

            'btn_match_show'        => $deposit->checkMatch(),
            'btn_unmatch_show'      => $deposit->checkUnmatch(),
            'btn_final_approve_show'=> $deposit->checkFinalApprove(),
            'need_second_approve'   => (int)$deposit->need_second_approve,
            'button_flow_code'      => $deposit->button_flow_code,

            'deposit_at'            => $deposit->deposit_at,
            'approved_at'           => convert_time($deposit->approved_at),
            'created_at'            => convert_time($deposit->created_at),
            # 上传凭证时间
            'receipt_img_created_at'  => convert_time($deposit->receipt_img_created_at),

            // display for frontend
            'display_online_banking_channel' => $deposit->online_banking_channel ? transfer_show_value($deposit->online_banking_channel, PaymentPlatform::$onlineBankingChannels) : "",
            'display_payment_platform' => $deposit->online_banking_channel ? transfer_show_value($deposit->online_banking_channel, PaymentPlatform::$onlineBankingChannels) : ($deposit->paymentPlatform ? $deposit->paymentPlatform->name  : ""),
            'display_status' => $deposit->status === Deposit::STATUS_HOLD ? "HD:" . $deposit->hold_reason  : transfer_show_value($deposit->status, Deposit::$statues),
            'display_tag' => transfer_show_value($deposit->tag, Deposit::$tags),
        ];
    }

    public function includeUser(Deposit $deposit) {
        return $this->item($deposit->user, new UserTransformer());
    }

    public function includePaymentPlatform(Deposit $deposit) {
        if($deposit->paymentPlatform) {
            return $this->item($deposit->paymentPlatform, new PaymentPlatformSimpleTransformer('', 'index'));
        }
        else {
            return null;
        }
    }

    public function includeUserAccount(Deposit $deposit) {
        return $this->item($deposit->user->account, new UserAccountTransformer());
    }

    public function includeUserInfo(Deposit $deposit) {
        return $this->item($deposit->user->info, new UserInfoTransformer());
    }

    public function includeUserBank(Deposit $deposit) {

        if($deposit->userBank) {
            return $this->item($deposit->userBank, new BankTransformer());
        }
        else {
            return null;
        }
    }

    public function includeBankTransaction(Deposit $deposit) {

        if($deposit->bankTransaction) {
            return $this->item($deposit->bankTransaction, new BankTransactionTransformer());
        }
        else {
            return null;
        }
    }

    public function includeAccessLogs(Deposit $deposit) {
        return $this->collection($deposit->accessLogs(), new DepositLogTransformer());
    }

    public function includeOperationLogs(Deposit $deposit) {
        return $this->collection($deposit->activeLogs(), new DepositLogTransformer());
    }

    public function includeUserDeposits(Deposit $deposit) {
        return $this->collection($deposit->user->lastDeposits($deposit->id, 10), new UserDepositTransformer());
    }

    public function includeImages(Deposit $deposit)
    {
        return $this->collection($deposit->images, new ImageTransformer());
    }
}
