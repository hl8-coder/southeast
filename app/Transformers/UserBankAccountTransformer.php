<?php

namespace App\Transformers;

use App\Models\Model;
use App\Models\UserBankAccount;

/**
 * @OA\Schema(
 *   schema="UserBankAccount",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="ID"),
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="user_name", type="string", description="会员名"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="bank_id", type="integer", description="银行id"),
 *   @OA\Property(property="bank_name", type="string", description="银行名称"),
 *   @OA\Property(property="bank_front_name", type="string", description="银行前端显示名称"),
 *   @OA\Property(property="province", type="string", description="省"),
 *   @OA\Property(property="city", type="string", description="市"),
 *   @OA\Property(property="branch", type="string", description="分行"),
 *   @OA\Property(property="is_preferred", type="string", description="是否首选"),
 *   @OA\Property(property="account_name", type="string", description="户名"),
 *   @OA\Property(property="account_no", type="string", description="卡号"),
 *   @OA\Property(property="status", type="integer",description="状态"),
 *   @OA\Property(property="last_used_at", type="string",description="最近使用时间", format="date-time"),
 *   @OA\Property(property="created_at", type="string",description="创建时间", format="date-time"),
 *   @OA\Property(property="updated_at", type="string",description="更新时间", format="date-time"),
 *   @OA\Property(property="bank", ref="#/components/schemas/Bank"),
 * )
 */
class UserBankAccountTransformer extends Transformer
{

    protected $availableIncludes = ['bank'];

    public function transform(UserBankAccount $userBankAccount)
    {
        $data = [
            'id'              => $userBankAccount->id,
            'user_id'         => $userBankAccount->user_id,
            'user_name'       => $userBankAccount->user->name,
            'currency'        => $userBankAccount->user->currency,
            'bank_id'         => $userBankAccount->bank_id,
            'bank_name'       => is_object($userBankAccount->bank) ? $userBankAccount->bank->name : '',
            'bank_front_name' => is_object($userBankAccount->bank) ? $userBankAccount->bank->front_name : '',
            'province'        => $userBankAccount->province,
            'city'            => $userBankAccount->city,
            'branch'          => $userBankAccount->branch,
            'is_preferred'    => transfer_show_value($userBankAccount->is_preferred, Model::$booleanDropList),
            'account_name'    => $userBankAccount->account_name,
            'account_no'      => hidden_number($userBankAccount->account_no, 4),
            'status'          => transfer_show_value($userBankAccount->status, UserBankAccount::$statuses),
            'last_used_at'    => convert_time($userBankAccount->last_used_at),
            'created_at'      => convert_time($userBankAccount->created_at),
            'updated_at'      => convert_time($userBankAccount->updated_at),
        ];
        switch ($this->type) {
            case 'affiliate_bank_account':
                $data['account_no'] = $userBankAccount->account_no;
                break;
        }
        return $data;
    }

    public function includeBank(UserBankAccount $userBankAccount)
    {
        return $this->item($userBankAccount->bank, new BankTransformer());
    }
}
