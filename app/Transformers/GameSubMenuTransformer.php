<?php

namespace App\Transformers;

use App\Models\Game;
use App\Models\Model;
use App\Models\GamePlatformProduct;

/**
 * @OA\Schema(
 *   schema="GameSubMenu",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="游戏id"),
 *   @OA\Property(property="code", type="integer", description="辨识码"),
 *   @OA\Property(property="type", type="integer", description="游戏类型"),
 *   @OA\Property(property="display_type", type="string", description="游戏类型显示"),
 *   @OA\Property(property="web_img_path", type="string", description="web端图片"),
 *   @OA\Property(property="mobile_img_path", type="string", description="mobile端图片"),
 *   @OA\Property(property="mobile_img_path_2", type="string", description="mobile端图片"),
 *   @OA\Property(property="small_img_path", type="string", description="游戏小图标"),
 *   @OA\Property(property="is_hot", type="boolean", description="是否热门"),
 *   @OA\Property(property="display_is_hot", type="string", description="是否热门显示"),
 *   @OA\Property(property="is_new", type="boolean", description="是否最新"),
 *   @OA\Property(property="display_is_new", type="string", description="是否最新显示"),
 *   @OA\Property(property="is_iframe", type="boolean", description="是否是 iframe 打开游戏"),
 *   @OA\Property(property="is_mobile_iframe", type="boolean", description="移动端是否是 iframe 打开游戏"),
 *   @OA\Property(property="display_is_iframe", type="string", description="是否是 iframe 打开游戏显示"),
 *   @OA\Property(property="display_is_mobile_iframe", type="string", description="移动端是否是 iframe 打开游戏显示"),
 *   @OA\Property(property="is_can_try", type="integer", description="是否可以试玩"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 *   @OA\Property(property="status", type="boolean", description="状态"),
 *   @OA\Property(property="is_soon", type="boolean", description="是否即将发布"),
 *   @OA\Property(property="display_is_soon", type="string", description="显示是否即将发布"),
 *   @OA\Property(property="display_status", type="string", description="状态显示"),
 *   @OA\Property(property="languages", type="array", description="语言", @OA\Items(
 *      @OA\Property(property="language", type="string", description="语言"),
 *      @OA\Property(property="name", type="string", description="名称"),
 *      @OA\Property(property="description", type="string", description="描述"),
 *      @OA\Property(property="content", type="string", description="内容"),
 *   )),
 * )
 */
class GameSubMenuTransformer extends Transformer
{
    public function transform($records)
    {
        $games = [];
        foreach ($records as $record) {
            $game = [
                'id'                       => $record->id,
                'code'                     => $record->code,
                'type'                     => $record->type,
                'display_type'             => transfer_show_value($record->type, GamePlatformProduct::$types),
                'web_img_path'             => empty($record->web_img_path) ? '' : get_image_url($record->web_img_path),
                'web_menu_img_path'        => empty($record->web_menu_img_path) ? '' : get_image_url($record->web_menu_img_path),
                'mobile_img_path'          => empty($record->mobile_img_path) ? '' : get_image_url($record->mobile_img_path),
                'mobile_img_path_2'        => empty($record->mobile_img_path_2) ? '' : get_image_url($record->mobile_img_path_2),
                'small_img_path'           => empty($record->small_img_path) ? '' : get_image_url($record->small_img_path),
                'is_hot'                   => $record->is_hot,
                'display_is_hot'           => transfer_show_value($record->is_hot, Model::$booleanDropList),
                'is_new'                   => $record->is_new,
                'display_is_new'           => transfer_show_value($record->is_new, Model::$booleanDropList),
                'is_iframe'                => $record->is_iframe,
                'is_mobile_iframe'         => $record->is_mobile_iframe,
                'display_is_iframe'        => transfer_show_value($record->is_iframe, Model::$booleanDropList),
                'display_is_mobile_iframe' => transfer_show_value($record->is_mobile_iframe, Model::$booleanDropList),
                'is_can_try'               => isset($record->product) ? $record->product->is_can_try : '',
                'sort'                     => $record->sort,
                'is_soon'                  => $record->is_soon,
                'display_is_soon'          => transfer_show_value($record->is_soon, Model::$booleanStatusesDropList),
            ];

            $languageSet         = $record->getLanguageSet(app()->getLocale());
            $game['name']        = $languageSet['name'];
            $game['description'] = $languageSet['description'];
            $game['content']     = $languageSet['content'];
            $game['language']    = $languageSet['language'];
            $game['is_maintain'] = isset($record->platform) ? (int)$record->platform->is_maintain : ''; # 前端接受 boolean 值为 0 1
            if (1 != $game['is_maintain']) {
                $games[] = $game;
            }
        }
        return $games;
    }
}
