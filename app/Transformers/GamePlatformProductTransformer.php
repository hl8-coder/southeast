<?php

namespace App\Transformers;

use App\Models\GamePlatformProduct;
use App\Models\Model;
use App\Models\User;

/**
 * @OA\Schema(
 *   schema="GamePlatformProduct",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="platform_code", type="string", description="平台code"),
 *   @OA\Property(property="platform_name", type="string", description="平台名称"),
 *   @OA\Property(property="code", type="string", description="辨识码"),
 *   @OA\Property(property="type", type="integer", description="类型"),
 *   @OA\Property(property="display_type", type="string", description="类型显示"),
 *   @OA\Property(property="devices", type="array", description="装置", @OA\Items()),
 *   @OA\Property(property="display_devices", type="array", description="装置显示", @OA\Items()),
 *   @OA\Property(property="is_close_bonus", type="boolean", description="是否可用于关闭红利"),
 *   @OA\Property(property="display_is_close_bonus", type="string", description="是否可用于关闭红利显示"),
 *   @OA\Property(property="is_close_cash_back", type="boolean", description="是否可用于关闭赎返"),
 *   @OA\Property(property="display_is_close_cash_back", type="string", description="是否可用于关闭赎返显示"),
 *   @OA\Property(property="is_close_adjustment", type="boolean", description="是否可用于关闭调整"),
 *   @OA\Property(property="display_is_close_adjustment", type="string", description="是否可用于关闭调整"),
 *   @OA\Property(property="is_calculate_reward", type="boolean", description="是否可用于计算积分"),
 *   @OA\Property(property="display_is_calculate_reward", type="string", description="是否可用于计算积分显示"),
 *   @OA\Property(property="is_calculate_cash_back", type="boolean", description="是否可用于计算赎返"),
 *   @OA\Property(property="display_is_calculate_cash_back", type="string", description="是否可用于计算赎返显示"),
 *   @OA\Property(property="is_calculate_rebate", type="boolean", description="是否可用于计算返点"),
 *   @OA\Property(property="is_can_try", type="boolean", description="是否可以试玩"),
 *   @OA\Property(property="display_is_calculate_rebate", type="string", description="是否可用于计算返点显示"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 *   @OA\Property(property="status", type="boolean", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="状态显示"),
 *   @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
 *   )),
 *     @OA\Property(property="languages", type="array", description="语言", @OA\Items(
 *      @OA\Property(property="language", type="string", description="语言"),
 *      @OA\Property(property="name", type="string", description="名称"),
 *      @OA\Property(property="description", type="string", description="描述"),
 *      @OA\Property(property="content", type="string", description="内容"),
 *      @OA\Property(property="one_web_img_path", type="string", description="PC端图片链接1"),
 *      @OA\Property(property="two_web_img_path", type="string", description="PC端图片链接2"),
 *      @OA\Property(property="mobile_img_path", type="string", description="手机端图片链接"),
 *   )),
 *   @OA\Property(property="platform", ref="#/components/schemas/GamePlatform"),
 * )
 */
class GamePlatformProductTransformer extends Transformer
{
    protected $availableIncludes = ['platform'];

    public function transform(GamePlatformProduct $product)
    {
        $data = [
            'id'                             => $product->id,
            'platform_code'                  => $product->platform_code,
            'platform_name'                  => $product->platform->name,
            'icon'                           => $product->platform->icon,
            'code'                           => $product->code,
            'type'                           => $product->type,
            'display_type'                   => transfer_show_value($product->type, GamePlatformProduct::$types),
            'currencies'                     => $product->currencies,
            'languages'                      => $product->languages,
            'devices'                        => $product->devices,
            'display_devices'                => transfer_array_show_value($product->devices, User::$devices),
            'is_close_bonus'                 => $product->is_close_bonus,
            'display_is_close_bonus'         => transfer_show_value($product->is_close_bonus, Model::$booleanDropList),
            'is_close_cash_back'             => $product->is_close_cash_back,
            'display_is_close_cash_back'     => transfer_show_value($product->is_close_cash_back, Model::$booleanDropList),
            'is_close_adjustment'            => $product->is_close_adjustment,
            'display_is_close_adjustment'    => transfer_show_value($product->is_close_adjustment, Model::$booleanDropList),
            'is_calculate_reward'            => $product->is_calculate_reward,
            'display_is_calculate_reward'    => transfer_show_value($product->is_calculate_reward, Model::$booleanDropList),
            'is_calculate_cash_back'         => $product->is_calculate_cash_back,
            'display_is_calculate_cash_back' => transfer_show_value($product->is_calculate_cash_back, Model::$booleanDropList),
            'is_calculate_rebate'            => $product->is_calculate_rebate,
            'display_is_calculate_rebate'    => transfer_show_value($product->is_calculate_rebate, Model::$booleanDropList),
            'is_can_try'                     => $product->is_can_try,
            'sort'                           => $product->sort,
            'status'                         => $product->status,
            'display_status'                 => transfer_show_value($product->status, Model::$booleanStatusesDropList),
        ];

        switch ($this->type) {
            case 'front_index':
                $languageSet = $product->getLanguageSet(app()->getLocale());
                $data        = collect($data)->only([
                    'id',
                    'platform_code',
                    'platform_name',
                    'icon',
                    'code',
                    'type',
                    'sort',
                    'one_web_img_path',
                    'two_web_img_path',
                    'mobile_img_path',
                    'is_can_try',
                ])->toArray();

                $data['name']        = $languageSet['name'];
                $data['description'] = $languageSet['description'];
                $data['content']     = $languageSet['content'];
                $data['language']    = $languageSet['language'];
                $data = $this->getImgPath($data, $languageSet);
                break;
            default:
                $data['languages'] = $this->getBOImgPath($data['languages']);
                break;
        }

        return $data;
    }

    public function includePlatform(GamePlatformProduct $product)
    {
        return $this->item($product->platform, new GamePlatformTransformer('associate'));
    }

    public function getImgPath($data, $languageSet)
    {
        foreach (GamePlatformProduct::$imgFields as $img) {
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
                if (in_array($k, array_values(GamePlatformProduct::$imgFields))) {
                    $language[$k] = !empty($v) ? get_image_url($v) : '';
                }
            }
        }
        return $languages;
    }
}
