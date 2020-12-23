<?php
namespace App\Transformers;

use App\Models\CompanyBankAccount;
use App\Models\Withdrawal;
use App\Repositories\WithdrawalRepository;

/**
 * @OA\Schema(
 *   schema="Withdrawal",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="提现id"),
 *   @OA\Property(property="order_no", type="integer", description="订单号"),
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="user_ip", type="string", description="提现ip"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="amount", type="number", description="提现金额"),
 *   @OA\Property(property="remain_amount", type="number", description="未出款金额"),
 *   @OA\Property(property="fee", type="number", description="手续费"),
 *   @OA\Property(property="bank_id", type="integer", description="银行id"),
 *   @OA\Property(property="province", type="string", description="省"),
 *   @OA\Property(property="city", type="string", description="市"),
 *   @OA\Property(property="branch", type="string", description="支行"),
 *   @OA\Property(property="account_no", type="string", description="卡号"),
 *   @OA\Property(property="account_name", type="string", description="户名"),
 *   @OA\Property(property="hold_reason", type="integer", description="理由"),
 *   @OA\Property(property="display_hold_reason", type="string", description="理由文字显示"),
 *   @OA\Property(property="reject_reason", type="integer", description="拒绝理由"),
 *   @OA\Property(property="display_reject_reason", type="string", description="拒绝理由文字显示"),
 *   @OA\Property(property="escalate_reason", type="integer", description="提升理由"),
 *   @OA\Property(property="display_escalate_reason", type="string", description="提升理由文字显示"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="显示状态"),
 *   @OA\Property(property="paid_at", type="string", description="支付时间", format="date-time"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 *   @OA\Property(property="last_access_at", type="string", description="最后查看时间", format="date-time"),
 *   @OA\Property(property="last_access_name", type="string", description="最后查看管理员名称"),
 *   @OA\Property(property="color", type="string", description="列表时显示颜色"),
 *   @OA\Property(property="cell_color", type="string", description="单元格显示颜色"),
 *   @OA\Property(property="hold_btn_is_show", type="boolean", description="HOLD按钮是否显示"),
 *   @OA\Property(property="release_hold_btn_is_show", type="boolean", description="RELEASE HOLD按钮是否显示"),
 *   @OA\Property(property="cancel_btn_is_show", type="boolean", description="CANCEL按钮是否显示"),
 *   @OA\Property(property="review_btn_is_show", type="boolean", description="REVIEW按钮是否显示"),
 *   @OA\Property(property="escalate_btn_is_show", type="boolean", description="ESCALATE按钮是否显示"),
 *   @OA\Property(property="manual_btn_is_show", type="boolean", description="MANUAL按钮是否显示"),
 *   @OA\Property(property="defer_btn_is_show", type="boolean", description="DEFER按钮是否显示"),
 *   @OA\Property(property="release_defer_btn_is_show", type="boolean", description="RELEASE DEFER按钮是否显示"),
 *   @OA\Property(property="approve_btn_is_show", type="boolean", description="APPROVE按钮是否显示"),
 *   @OA\Property(property="add_transactions_btn_is_show", type="boolean", description="是否显示添加出款按钮"),
 *   @OA\Property(property="second_approve_btn_is_show", type="boolean", description="是否显示二次同意按钮"),
 *   @OA\Property(property="second_reject_btn_is_show", type="boolean", description="是否显示二次拒绝按钮"),
 *   @OA\Property(property="rm_approve_btn_is_show", type="boolean", description="是否显示rm审核同意按钮"),
 *   @OA\Property(property="is_close_form", type="boolean", description="是否关闭表单"),
 *   @OA\Property(property="paid_amount", type="number", description="已出款金额"),
 *   @OA\Property(property="paid_fee", type="number", description="已出款手续费"),
 *   @OA\Property(property="is_open_detail", type="bool", description="是否可以打开详情"),
 *   @OA\Property(property="user", description="会员信息", ref="#/components/schemas/User"),
 *   @OA\Property(property="bank", description="银行信息", ref="#/components/schemas/Bank"),
 *   @OA\Property(property="records", description="出款记录", type="array", @OA\Items(
 *      @OA\Property(property="company_bank_account_code", type="string", description="公司银行卡code"),
 *      @OA\Property(property="amount", type="number", description="出账金额"),
 *      @OA\Property(property="fee", type="number", description="手续费"),
 *   )),
 *   @OA\Property(property="last_ten_withdrawals", description="最近10笔提现记录", type="array", @OA\Items()),
 *   @OA\Property(property="company_bank_account_code", description="可用提现银行卡列表", type="array", @OA\Items()),
 *   @OA\Property(property="verify_details", description="审核明细", type="array", @OA\Items(
 *      @OA\Property(property="is_poker_player", type="boolean", description="是否是扑克玩家"),
 *      @OA\Property(property="is_has_remark", type="boolean", description="是否存在未移除的remark"),
 *      @OA\Property(property="deposit_turnover", type="number", description="充值所需流水"),
 *      @OA\Property(property="deposit_is_closed", type="boolean", description="充值所需流水是否达标"),
 *      @OA\Property(property="is_has_bonus", type="boolean", description="是否存在红利"),
 *      @OA\Property(property="total_turnover", type="number", description="所需总流水"),
 *      @OA\Property(property="total_not_close_turnover", type="number", description="未关闭总流水"),
 *      @OA\Property(property="turnover_is_closed", type="boolean", description="所需总流水是否达标"),
 *      @OA\Property(property="is_has_adjustment", type="boolean", description="账户是否存在调整"),
 *   )),
 *    @OA\Property(property="audits", description="修改记录", type="array", @OA\Items(
 *      @OA\Property(property="action", type="string", description="动作"),
 *      @OA\Property(property="description", type="string", description="描述"),
 *      @OA\Property(property="admin_name", type="string", description="操作管理员名称"),
 *      @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 *   )),
 * )
 */
class WithdrawalTransformer extends Transformer
{
    protected $availableIncludes = ['user', 'bank', 'images', 'userWithdrawals'];

    public function transform(Withdrawal $withdrawal)
    {
        $data = [
            'id'                        => $withdrawal->id,
            'order_no'                  => $withdrawal->order_no,
            'user_id'                   => $withdrawal->user_id,
            'user_ip'                   => $withdrawal->user_ip,
            'currency'                  => $withdrawal->user->currency,
            'amount'                    => thousands_number($withdrawal->amount, 3),
            'remain_amount'             => $withdrawal->remain_amount,
            'fee'                       => $withdrawal->fee,
            'remain_fee'                => $withdrawal->remain_fee,
            'bank_id'                   => $withdrawal->bank_id,
            'province'                  => $withdrawal->province,
            'city'                      => $withdrawal->city,
            'branch'                    => $withdrawal->branch,
            'account_no'                => $withdrawal->account_no,
            'account_name'              => $withdrawal->account_name,
            'hold_reason'               => $withdrawal->hold_reason,
            'display_hold_reason'       => transfer_show_value($withdrawal->hold_reason, Withdrawal::$holdReasons),
            'reject_reason'             => $withdrawal->reject_reason,
            'display_reject_reason'     => transfer_show_value($withdrawal->reject_reason, Withdrawal::$rejectReasons),
            'escalate_reason'           => $withdrawal->escalate_reason,
            'display_escalate_reason'   => transfer_show_value($withdrawal->escalate_reason, Withdrawal::$escalateReasons),
            'status'                    => transfer_show_value($withdrawal->status, Withdrawal::$statuses),
            'display_status'            => transfer_show_value($withdrawal->status, Withdrawal::$statuses),
            'remark'                    => $withdrawal->remark,
            'paid_at'                   => convert_time($withdrawal->paid_at),
            'created_at'                => convert_time($withdrawal->created_at),
            'last_access_name'          => $withdrawal->last_access_name,
            'last_access_at'            => convert_time($withdrawal->last_access_at),
        ];

        switch ($this->type) {
            case 'index':
                $data['records']        = $withdrawal->records;

                break;
            case 'open_index':
                $color = '';
                $cellColor = '';
                $almostMinutes = $withdrawal->updated_at->diffInMinutes(now());

                if ($withdrawal->isPending()) {
                    if ($withdrawal->user->isVip()) {
                        $color = '#fb1c2b';
                    } elseif ($almostMinutes >= 10) {
                        $color = '#ef9f71';
                    } elseif ($almostMinutes >= 2) {
                        $color = '#fee287';
                    }

                    if ($withdrawal->isApproved()) {
                        $cellColor = '#bbedc3';
                    } elseif ($withdrawal->isRejected()) {
                        $cellColor = '#fdb9c3';
                    }
                }

                $data['color']      = $color;
                $data['cell_color'] = $cellColor;
                $data['is_open_detail'] = empty($withdrawal->claim_admin_name)
                    || WithdrawalRepository::isClaimAdmin($withdrawal, $this->data['admin'])
                    || $withdrawal->isEscalate();
                break;
            case 'front':
                $data = collect($data)->only([
                    'id',
                    'order_no',
                    'user_id',
                    'amount',
                    'bank_id',
                    'account_no',
                ])->toArray();
                break;
            default:
                $companyBankAccountCodes = CompanyBankAccount::getWithdrawalTypeAccount($withdrawal->currency)->pluck('code', 'code')->toArray();
                $data['verify_details']                  = $withdrawal->isSuccessful() ? $withdrawal->verify_details : WithdrawalRepository::getVerifyDetails($withdrawal->user_id);
                $data['records']                         = $withdrawal->records;
                $data['paid_amount']                     = collect($withdrawal->records)->sum('amount');
                $data['paid_fee']                        = collect($withdrawal->records)->sum('fee');
                $data['audits']                          = WithdrawalRepository::getAudits($withdrawal);
                $data['last_ten_withdrawals']            = [];
                $data['hold_btn_is_show']                = $withdrawal->checkCanHoldStatus();
                $data['release_hold_btn_is_show']        = $withdrawal->isHold();
                $data['cancel_btn_is_show']              = $withdrawal->checkCanRejectStatus();
                $data['review_btn_is_show']              = false;
                $data['claim_btn_is_show']               = empty($withdrawal->claim_admin_name) && $withdrawal->isPending();
                $data['unclaim_btn_is_show']             = $withdrawal->checkCanUnclaimStatus();
                $data['escalate_btn_is_show']            = empty($withdrawal->claim_admin_name) && $withdrawal->isPending();
                $data['manual_btn_is_show']              = !empty($withdrawal->claim_admin_name) && $withdrawal->isPending();
                $data['defer_btn_is_show']               = $withdrawal->isProcess();
                $data['defer_btn_is_show']               = false;
                $data['release_defer_btn_is_show']       = $withdrawal->isDeferred();
                $data['approve_btn_is_show']             = $withdrawal->isProcess();
                $data['add_transactions_btn_is_show']    = $withdrawal->isProcess();
                $data['rm_approve_btn_is_show']          = $withdrawal->isEscalate();
                $data['second_approve_btn_is_show']      = $withdrawal->checkSecondVerifyStatus();
                $data['second_reject_btn_is_show']       = $withdrawal->checkSecondVerifyStatus();
                $data['is_close_form']                   = $withdrawal->is_close_form ?? false;
                $data['company_bank_account_code']       = transform_list($companyBankAccountCodes);
                break;
        }

        return $data;
    }

    public function includeUser(Withdrawal $withdrawal)
    {
        return $this->item($withdrawal->user, new UserTransformer());
    }

    public function includeBank(Withdrawal $withdrawal)
    {
        return $this->item($withdrawal->bank, new BankTransformer());
    }

    public function includeImages(Withdrawal $withdrawal)
    {
        return $this->collection($withdrawal->images, new ImageTransformer());
    }

    public function includeUserWithdrawals(Withdrawal $withdrawal) {
        $withdrawals = WithdrawalRepository::lastTenWithdrawal($withdrawal);
        return $this->collection($withdrawals, new UserWithdrawalTransformer());
    }
}