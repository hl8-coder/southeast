<?php

namespace App\Transformers;

use App\Models\Game;
use App\Models\GamePlatformProduct;
use App\Models\Model;
use App\Models\User;

/**
 * @OA\Schema(
 *   schema="Game",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="游戏id"),
 *   @OA\Property(property="platform_code", type="string", description="平台code"),
 *   @OA\Property(property="product_code", type="string", description="产品code"),
 *   @OA\Property(property="code", type="integer", description="辨识码"),
 *   @OA\Property(property="back_name", type="string", description="后端显示名称"),
 *   @OA\Property(property="type", type="integer", description="游戏类型"),
 *   @OA\Property(property="display_type", type="string", description="游戏类型显示"),
 *   @OA\Property(property="devices", type="array", description="装置", @OA\Items()),
 *   @OA\Property(property="display_devices", type="array", description="装置显示", @OA\Items()),
 *   @OA\Property(property="web_img_path", type="string", description="web端图片"),
 *   @OA\Property(property="mobile_img_path", type="string", description="mobile端图片"),
 *   @OA\Property(property="mobile_img_path_2", type="string", description="mobile端图片"),
 *   @OA\Property(property="small_img_path", type="string", description="游戏小图标"),
 *   @OA\Property(property="droplist_img_path", type="string", description="下拉游戏图片地址"),
 *   @OA\Property(property="is_hot", type="boolean", description="是否热门"),
 *   @OA\Property(property="display_is_hot", type="string", description="是否热门显示"),
 *   @OA\Property(property="is_new", type="boolean", description="是否最新"),
 *   @OA\Property(property="display_is_new", type="string", description="是否最新显示"),
 *   @OA\Property(property="is_iframe", type="boolean", description="是否是 iframe 打开游戏"),
 *   @OA\Property(property="is_mobile_iframe", type="boolean", description="移动端是否是 iframe 打开游戏"),
 *   @OA\Property(property="is_using_cookie", type="boolean", description="if the game uses cookie"),
 *   @OA\Property(property="display_is_iframe", type="string", description="是否是 iframe 打开游戏显示"),
 *   @OA\Property(property="display_is_mobile_iframe", type="string", description="移动端是否是 iframe 打开游戏显示"),
 *   @OA\Property(property="is_close_bonus", type="boolean", description="是否可用于关闭红利"),
 *   @OA\Property(property="is_effective_bet", type="boolean", description="是否可计算有效投注"),
 *   @OA\Property(property="display_is_close_bonus", type="string", description="是否可用于关闭红利显示"),
 *   @OA\Property(property="is_close_cash_back", type="boolean", description="是否可用于关闭赎返"),
 *   @OA\Property(property="display_is_close_cash_back", type="string", description="是否可用于关闭赎返显示"),
 *   @OA\Property(property="is_calculate_reward", type="boolean", description="是否可用于计算积分"),
 *   @OA\Property(property="display_is_calculate_reward", type="string", description="是否可用于计算积分显示"),
 *   @OA\Property(property="is_calculate_cash_back", type="boolean", description="是否可用于计算赎返"),
 *   @OA\Property(property="display_is_calculate_cash_back", type="string", description="是否可用于计算赎返显示"),
 *   @OA\Property(property="is_calculate_rebate", type="boolean", description="是否可用于计算返点"),
 *   @OA\Property(property="display_is_calculate_rebate", type="string", description="是否可用于计算返点显示"),
 *   @OA\Property(property="is_can_try", type="integer", description="是否可以试玩"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 *   @OA\Property(property="status", type="boolean", description="状态"),
 *   @OA\Property(property="is_soon", type="boolean", description="是否即将发布"),
 *   @OA\Property(property="platform_name", type="string", description="钱包名称"),
 *   @OA\Property(property="display_is_soon", type="string", description="显示是否即将发布"),
 *   @OA\Property(property="display_status", type="string", description="状态显示"),
 *   @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
 *   )),
 *   @OA\Property(property="languages", type="array", description="语言", @OA\Items(
 *      @OA\Property(property="language", type="string", description="语言"),
 *      @OA\Property(property="name", type="string", description="名称"),
 *      @OA\Property(property="description", type="string", description="描述"),
 *      @OA\Property(property="content", type="string", description="内容"),
 *   )),
 * )
 */
class GameTransformer extends Transformer
{
    public function transform(Game $game)
    {
        $backLanguageSet = $game->getLanguageSet('en-US');

        switch ($this->type) {
            case 'no_slot_index':
                $languageSet         = $game->getLanguageSet(app()->getLocale());
                $data = [
                    'id'                 => $game->id,
                    'platform_code'      => $game->platform_code,
                    'code'               => $game->code,
                    'name'               => !empty($languageSet['name']) ? $languageSet['name'] : '',
                    'type'               => $game->type,
                    'is_iframe'          => $game->is_iframe,
                    'is_mobile_iframe'   => $game->is_mobile_iframe,
                    'is_using_cookie'    => $game->is_using_cookie,
                    'is_can_try'         => $game->product->is_can_try,
                    'is_soon'            => $game->is_soon,
                    'platform_name'      => $game->platform->name,
                    'is_maintain'        => (int)$game->platform->is_maintain,
                ];

                $data = $this->getImgPath($data, $languageSet);
                break;

            default:
                $data = [
                    'id'                             => $game->id,
                    'platform_code'                  => $game->platform_code,
                    'product_code'                   => $game->product_code,
                    'code'                           => $game->code,
                    'back_name'                      => !empty($backLanguageSet['name']) ? $backLanguageSet['name'] : '',
                    'devices'                        => $game->devices,
                    'display_devices'                => transfer_array_show_value($game->devices, User::$devices),
                    'type'                           => $game->type,
                    'display_type'                   => transfer_show_value($game->type, GamePlatformProduct::$types),
                    'is_hot'                         => $game->is_hot,
                    'display_is_hot'                 => transfer_show_value($game->is_hot, Model::$booleanDropList),
                    'is_new'                         => $game->is_new,
                    'display_is_new'                 => transfer_show_value($game->is_new, Model::$booleanDropList),
                    'is_iframe'                      => $game->is_iframe,
                    'is_mobile_iframe'               => $game->is_mobile_iframe,
                    'is_using_cookie'                => $game->is_using_cookie,
                    'display_is_iframe'              => transfer_show_value($game->is_iframe, Model::$booleanDropList),
                    'display_is_mobile_iframe'       => transfer_show_value($game->is_mobile_iframe, Model::$booleanDropList),
                    'is_effective_bet'               => $game->is_effective_bet,
                    'is_close_bonus'                 => $game->is_close_bonus,
                    'display_is_close_bonus'         => transfer_show_value($game->is_close_bonus, Model::$booleanDropList),
                    'is_close_cash_back'             => $game->is_close_cash_back,
                    'display_is_close_cash_back'     => transfer_show_value($game->is_close_cash_back, Model::$booleanDropList),
                    'is_calculate_reward'            => $game->is_calculate_reward,
                    'display_is_calculate_reward'    => transfer_show_value($game->is_calculate_reward, Model::$booleanDropList),
                    'is_calculate_cash_back'         => $game->is_calculate_cash_back,
                    'display_is_calculate_cash_back' => transfer_show_value($game->is_calculate_cash_back, Model::$booleanDropList),
                    'is_calculate_rebate'            => $game->is_calculate_rebate,
                    'display_is_calculate_rebate'    => transfer_show_value($game->is_calculate_rebate, Model::$booleanDropList),
                    'is_can_try'                     => $game->product->is_can_try,
                    'remark'                         => $game->remark,
                    'sort'                           => $game->sort,
                    'status'                         => $game->status,
                    'display_status'                 => transfer_show_value($game->status, Model::$booleanStatusesDropList),
                    'is_soon'                        => $game->is_soon,
                    'display_is_soon'                => transfer_show_value($game->is_soon, Model::$booleanStatusesDropList),
                    'currencies'                     => $game->currencies,
                    'languages'                      => $game->languages,
                    'last_save_admin'                => $game->last_save_admin,
                    'last_save_at'                   => $game->last_save_at,

                ];

                $data['languages'] = $this->getBOImgPath($data['languages']);
                break;
        }



        switch ($this->type) {
            case 'front_index':
                $languageSet         = $game->getLanguageSet(app()->getLocale());
                $data                = collect($data)->only([
                    'id',
                    'platform_code',
                    'product_code',
                    'code',
                    'type',
                    'sort',
                    'is_hot',
                    'is_new',
                    'is_iframe',
                    'is_mobile_iframe',
                    'is_using_cookie',
                    'web_img_path',
                    'mobile_img_path',
                    'mobile_img_path_2',
                    'droplist_img_path',
                    'is_can_try',
                    'small_img_path',
                    'is_soon',
                ])->toArray();
                $data['name']               = $languageSet['name'];
                $data['description']        = $languageSet['description'];
                $data['content']            = $languageSet['content'];
                $data['language']           = $languageSet['language'];
                $data['platform_name']      = $game->platform->name;
                $data['is_maintain']        = (int)$game->platform->is_maintain; # 前端接受 boolean 值为 0 1

                $data = $this->getImgPath($data, $languageSet);
                break;
        }

        return $data;
    }

    public function getImgPath($data, $languageSet)
    {
        foreach (Game::$imgFields as $img) {
            if (!empty($languageSet[$img])) {
                $data[$img] = get_image_url($languageSet[$img]);
            } else {
                $data[$img] = '';
            }
        }
        return $data;
    }

    public function getBOImgPath($languages)
    {
        foreach ($languages as &$language) {
            foreach ($language as $k => $v) {
                if (in_array($k, array_values(Game::$imgFields))) {
                    $language[$k] = !empty($v) ? get_image_url($v) : '';
                }
            }
        }
        return $languages;
    }

}
