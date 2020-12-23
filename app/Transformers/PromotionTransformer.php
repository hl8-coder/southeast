<?php

namespace App\Transformers;

use App\Models\Model;
use App\Models\Promotion;
use App\Services\PromotionService;

/**
 * @OA\Schema(
 *   schema="Promotion",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="图片id"),
 *   @OA\Property(property="show_types", type="array", description="显示类型code", @OA\Items()),
 *   @OA\Property(property="promotion_type_code", type="string", description="优惠类型code"),
 *   @OA\Property(property="codes", type="array", description="关联code", @OA\Items()),
 *   @OA\Property(property="is_can_claim", type="boolean", description="是否可以报名"),
 *   @OA\Property(property="backstage_title", type="string", description="后台显示标题"),
 *   @OA\Property(property="display_start_at", type="string", description="上架时间", format="date-time"),
 *   @OA\Property(property="display_end_at", type="string", description="下架时间", format="date-time"),
 *   @OA\Property(property="web_img_path", type="string", description="PC端图片"),
 *   @OA\Property(property="web_content_img_path", type="string", description="PC端内容图片"),
 *   @OA\Property(property="mobile_img_path", type="string", description="Mobile端图片"),
 *   @OA\Property(property="mobile_content_img_path", type="string", description="Mobile端内容图片"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="状态显示"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 *   @OA\Property(property="is_claimed", type="boolean", description="是否已报名"),
 *   @OA\Property(property="is_agent", type="boolean", description="是否是代理"),
 *   @OA\Property(property="is_verified", type="boolean", description="是否需要验证"),
 *   @OA\Property(property="related_type", type="string", description="关联类型"),
 *   @OA\Property(property="mobile_language_image", type="string", description="适配多语言移动端图片"),
 *   @OA\Property(property="currency", type="string", description="币别显示"),
 *   @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
 *   )),
 *   @OA\Property(property="languages", type="array", description="语言", @OA\Items(
 *      @OA\Property(property="language", type="string", description="语言"),
 *      @OA\Property(property="title", type="string", description="前端显示标题"),
 *      @OA\Property(property="description", type="string", description="描述"),
 *      @OA\Property(property="content", type="string", description="内容"),
 *      @OA\Property(property="mobile_image", type="string", description="针对移动端的适配多语言配图"),
 *   )),
 * )
 */
class PromotionTransformer extends Transformer
{
    public function transform(Promotion $promotion)
    {
        $codes = !empty($promotion->codes) ? implode(',', $promotion->codes) : '';
        $data  = [
            'id'                      => $promotion->id,
            'show_types'              => $promotion->show_types,
            'promotion_type_code'     => $promotion->promotion_type_code,
            'code'                    => $promotion->code,
            'codes'                   => $codes,
            'is_can_claim'            => $promotion->is_can_claim,
            'backstage_title'         => $promotion->backstage_title,
            'display_start_at'        => convert_time($promotion->display_start_at),
            'display_end_at'          => convert_time($promotion->display_end_at),
            'web_img_path'            => get_image_url($promotion->web_img_path),
            'web_content_img_path'    => get_image_url($promotion->web_content_img_path),
            'mobile_img_path'         => get_image_url($promotion->mobile_img_path),
            'mobile_content_img_path' => get_image_url($promotion->mobile_content_img_path),
            'status'                  => $promotion->status,
            'display_status'          => transfer_show_value($promotion->status, Model::$booleanStatusesDropList),
            'sort'                    => $promotion->sort,
            'is_claimed'              => $promotion->is_claimed,
            'is_agent'                => $promotion->is_agent,
            'is_verified'             => $promotion->is_verified,
            'related_type'            => $promotion->related_type,
            'display_related_type'    => transfer_show_value($promotion->related_type, Promotion::$relatedTypes),
            'admin_name'              => $promotion->admin_name,
            'currencies'              => $promotion->currencies,
            'currency'                => implode(',', $promotion->currencies),
            'languages'               => $promotion->languages,
            'created_at'              => convert_time($promotion->created_at),
        ];

        switch ($this->type) {
            case 'front_index':
                $languageSet         = $promotion->getLanguageSet(app()->getLocale());
                $data                = collect($data)->only([
                    'id',
                    'code',
                    'promotion_type_code',
                    'codes',
                    'web_img_path',
                    'mobile_img_path',
                    'is_claimed',
                    'description',
                    'is_can_claim',
                    'currencies',
                ])->toArray();
                $data['codes']       = transform_list($this->getBonusRelation($promotion));
                $data['title']       = $languageSet['title'];
                $data['description'] = $languageSet['description'];
                $data['mobile_language_image'] = isset($languageSet['mobile_image']) ? get_image_url($languageSet['mobile_image']) : $data['mobile_img_path'];
                break;
            case 'front_show':
                $languageSet         = $promotion->getLanguageSet(app()->getLocale());
                $data                = collect($data)->only([
                    'id',
                    'code',
                    'promotion_type_code',
                    'codes',
                    'display_start_at',
                    'display_end_at',
                    'web_img_path',
                    'web_content_img_path',
                    'mobile_img_path',
                    'mobile_content_img_path',
                    'is_can_claim',
                    'is_claimed',
                    'currencies',
                ])->toArray();
                $data['codes']       = transform_list($this->getBonusRelation($promotion));
                $data['title']       = $languageSet['title'];
                $data['description'] = $languageSet['description'];
                $data['content']     = $languageSet['content'];
                $data['mobile_language_image'] = isset($languageSet['mobile_image']) ? get_image_url($languageSet['mobile_image']) : $data['mobile_img_path'];
                break;
            case 'backstage_index':
                $data = collect($data)->except([
                    'content',
                ])->toArray();
                $data['show_types'] = !empty($data['show_types']) ? implode(',', $data['show_types']) : '';
                # 专门用来展示图片的字段，不用提交上来
                foreach ($data['languages'] as &$languageContent){
                    $languageContent['mobile_language_image_show'] = isset($languageContent['mobile_image']) ? get_image_url($languageContent['mobile_image']) : '';
                }
                break;
            default:
                # 专门用来展示图片的字段，不用提交上来
                foreach ($data['languages'] as &$languageContent){
                    $languageContent['mobile_language_image_show'] = isset($languageContent['mobile_image']) ? get_image_url($languageContent['mobile_image']) : '';
                }
                break;
        }

        return $data;
    }

    private function getBonusRelation(Promotion $promotion)
    {
        $codes             = $promotion->codes;
        $relatedType       = $promotion->related_type;
        $promotionRelation = [];

        if (!empty($codes)) {
            foreach ($codes as $oneCode) {
                if (empty($oneCode)) {
                    continue;
                }
                $promotionRelation[$oneCode] = $this->getBonusTitle($oneCode, $relatedType);
            }
        }
        return $promotionRelation;
    }

    private function getBonusTitle($code, $relatedType)
    {
        $bonus            = (new PromotionService())->getRelatedModel($relatedType, $code);
        $bonusLanguageSet = empty($bonus) ? [] : $bonus->getLanguageSet(app()->getLocale());
        return isset($bonusLanguageSet['title']) ? $bonusLanguageSet['title'] : $code;
    }
}
