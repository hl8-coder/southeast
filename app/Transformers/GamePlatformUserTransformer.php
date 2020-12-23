<?php
namespace App\Transformers;

use App\Models\GamePlatformProduct;
use App\Models\GamePlatformUser;

/**
 * @OA\Schema(
 *   schema="GamePlatformUser",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="游戏平台会员id"),
 *   @OA\Property(property="platform_code", type="string", description="游戏平台code"),
 *   @OA\Property(property="platform_name", type="string", description="游戏平台名称"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="balance", type="number", description="余额"),
 *   @OA\Property(property="platform_created_at", type="string", description="创建时间", format="data-time"),
 *   @OA\Property(property="balance_status", type="string", description="第三方钱包状态"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="types", type="string", description="游戏类型"),
 * )
 */
class GamePlatformUserTransformer extends Transformer
{
    public function transform(GamePlatformUser $platformUser)
    {
        $data = [
            'id'                    => $platformUser->id,
            'platform_code'         => $platformUser->platform_code,
            'platform_name'         => !empty($platformUser->platform) ? $platformUser->platform->name : '',
            'currency'              => $platformUser->currency,
            'user_id'               => $platformUser->user_id,
            'balance'               => $platformUser->balance,
            'platform_created_at'   => convert_time($platformUser->platform_created_at),
            'balance_status'        => $platformUser->balance_status,
            'status'                => $platformUser->status,
        ];

        switch ($this->type) {
            case 'wallet':
                $types = GamePlatformProduct::getAll()->where('platform_code', $platformUser->platform_code)->pluck('type')->toArray();
                if ('IMSports' == $platformUser->platform_code) {
                    $types[] = GamePlatformProduct::TYPE_ESPORT;
                }
                $types = transfer_array_show_value($types, GamePlatformProduct::$types);
                $data['types'] = implode(',', $types);
                break;
            case 'backstage':
                $data['balance'] = thousands_number($data['balance']);
                break;
        }

        return $data;
    }
}