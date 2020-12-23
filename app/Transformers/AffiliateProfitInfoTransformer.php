<?php
namespace App\Transformers;

use App\Models\GamePlatform;
use App\Models\GamePlatformProduct;

/**
 * @OA\Schema(
 *   schema="AffiliateProfitInfo",
 *   type="object",
 *   @OA\Property(property="game_type", type="integer", description="游戏类型"),
 *   @OA\Property(property="platform_name", type="integer", description="平台名称"),
 *   @OA\Property(property="platform_code", type="integer", description="平台代码"),
 *   @OA\Property(property="platform_profit", type="string", description="平台盈亏"),
 *   @OA\Property(property="user_bet", type="string", description="会员投注"),
 *   @OA\Property(property="bet_count", type="string", description="投注数"),
 *   @OA\Property(property="active_count", type="integer", description="活跃数"),
 * )
 */
class AffiliateProfitInfoTransformer extends Transformer
{
    public function transform($data)
    {
        $gamePlatform = GamePlatform::getAll()->pluck('code', 'name')->toArray();

        return [
            'game_type'         => $data->game_type,
            'platform_name'     => transfer_show_value($data->platform_code, $gamePlatform),
            'platform_code'     => $data->platform_code,
            'platform_profit'   => thousands_number($data->platform_profit),
            'user_bet'          => thousands_number($data->user_bet),
            'bet_count'         => $data->bet_count,
            'active_count'      => $data->active_count,
            'display_game_type' => transfer_show_value($data->game_type, GamePlatformProduct::$types),
        ];
    }

}
