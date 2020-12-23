<?php

namespace App\Transformers;

use App\Models\Bank;
use App\Models\CompanyBankAccount;
use App\Models\PaymentGroup;
use App\Models\PgAccount;
use App\Models\PgAccountReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Schema(
 *   schema="PgAccount",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="pg account id"),
 *   @OA\Property(property="pg_account", type="string", description="pg account "),
 *   @OA\Property(property="name", type="string", description="pg account name "),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="payment_group_name_list", type="string", description="支付组名称"),
 *   @OA\Property(property="current_balance", type="number", description="当前余额"),
 *   @OA\Property(property="status", type="string", description="状态"),
 *   @OA\Property(property="deposit", type="number", description="存款金额"),
 *   @OA\Property(property="deposit_fee", type="string", description="存款手续费"),
 *   @OA\Property(property="withdraw", type="string", description="提款金额"),
 *   @OA\Property(property="withdraw_fee", type="string", description="提款手续费"),
 * )
 */
class PgAccountTransformer extends Transformer
{

    public function transform(PgAccount $account)
    {
        $inputData    = $this->data;
        $paymentGroup = $inputData['payment_group'];
        $groupInfo    = array_filter($paymentGroup, function ($item) use ($account) {
            $accountCode = $item['account_code'] ?? [];
            if (in_array($account->payment_platform_code, $accountCode)) {
                return true;
            } else {
                return false;
            }
        });

        $nameList = collect($groupInfo)->pluck('name')->toArray();

        $data = [
            'id'                      => $account->id,
            'pg_account'              => $account->payment_platform_code,
            'name'                    => !empty($account->paymentPlatform) ? $account->paymentPlatform->name : '-',
            'currency'                => !empty($account->paymentPlatform) ? $account->paymentPlatform->currencies : '-',
            'payment_group_name_list' => implode(',', $nameList),
            'current_balance'         => thousands_number($account->current_balance),
            'status'                  => transfer_show_value($account->status, PgAccount::$statuses),
        ];


        switch ($this->type) {
            case 'index':
                $data['deposit']      = thousands_number($account->deposit);
                $data['deposit_fee']  = thousands_number($account->deposit_fee);
                $data['withdraw']     = thousands_number($account->withdraw);
                $data['withdraw_fee'] = thousands_number($account->withdraw_fee);
                break;
        }

        return $data;
    }

}
