<?php
namespace App\Transformers;

use App\Models\UserAccount;

/**
 * @OA\Schema(
 *   schema="UserAccount",
 *   type="object",
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="available_balance", type="number", description="可用金额"),
 *   @OA\Property(property="total_point_balance", type="number", description="总积分"),
 * )
 */
class UserAccountTransformer extends Transformer
{
    public function transform(UserAccount $userAccount)
    {
        return [
            'user_id'                   => $userAccount->user_id,
            'available_balance'         => thousands_number($userAccount->total_balance - $userAccount->freeze_balance, 2),
            'total_point_balance'       => $userAccount->total_point_balance,
        ];
    }
}
