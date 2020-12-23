<?php

namespace App\Transformers;

use App\Models\Model;
use App\Models\Promotion;
use App\Models\PromotionType;

/**
 * @OA\Schema(
 *   schema="PromotionType",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="图片id"),
 *   @OA\Property(property="code", type="string", description="辨识码"),
 *   @OA\Property(property="web_img_path", type="string", description="PC端图片"),
 *   @OA\Property(property="mobile_img_path", type="string", description="Mobile端图片"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="状态显示"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 *   @OA\Property(property="admin_name", type="string", description="管理员"),
 *   @OA\Property(property="is_has_promotion", type="boolean", description="是否含有优惠"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
 *   )),
 *   @OA\Property(property="languages", type="array", description="语言", @OA\Items(
 *      @OA\Property(property="language", type="string", description="语言"),
 *      @OA\Property(property="title", type="string", description="前端显示标题"),
 *      @OA\Property(property="description", type="string", description="描述"),
 *   )),
 * )
 */
class PromotionTypeTransformer extends Transformer
{
    public function transform(PromotionType $promotionType)
    {
        $data = [
            'id'              => $promotionType->id,
            'currencies'      => $promotionType->currencies,
            'currency'        => implode(',', $promotionType->currencies),
            'languages'       => $promotionType->languages,
            'code'            => $promotionType->code,
            'web_img_path'    => strstr($promotionType->web_img_path, 'http') == false ? get_image_url($promotionType->web_img_path) : $promotionType->web_img_path,
            'mobile_img_path' => strstr($promotionType->mobile_img_path, 'http') == false ? get_image_url($promotionType->mobile_img_path) : $promotionType->mobile_img_path,
            'status'          => $promotionType->status,
            'display_status'  => transfer_show_value($promotionType->status, Model::$booleanStatusesDropList),
            'sort'            => $promotionType->sort,
            'admin_name'      => $promotionType->admin_name,
        ];

        switch ($this->type) {
            case 'front_index':
                $language                 = app()->getLocale();
                $currency                 = $this->data['currency'];
                $languageSet              = $promotionType->getLanguageSet($language);
                $data                     = collect($data)->except([
                    'status',
                    'display_status',
                    'sort',
                    'admin_name',
                    'currencies',
                ])->toArray();
                $data['title']            = $languageSet['title'];
                $data['description']      = $languageSet['description'];
                $now                      = now();
                $typeCode                 = $promotionType->code;
                $data['is_has_promotion'] = Promotion::getAll()->where('status', true)
                    ->filter(function ($value) use ($currency, $now, $typeCode) {
                        if (!empty($value->display_start_at) && $value->display_start_at > $now) {
                            return false;
                        }

                        if (!empty($value->display_end_at) && $value->display_end_at < $now) {
                            return false;
                        }

                        if (!empty($value->show_types) && !in_array($typeCode, $value->show_types)) {
                            return false;
                        }

                        return $value->checkCurrencySet($currency);
                    })
                    ->isNotEmpty();
                break;
        }

        return $data;
    }
}
