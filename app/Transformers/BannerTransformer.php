<?php

namespace App\Transformers;

use App\Models\Banner;
use App\Models\Model;

/**
 * @OA\Schema(
 *   schema="Banner",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="图片id"),
 *   @OA\Property(property="code", type="string", description="显示标题"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="languages", type="array", description="多语言",
 *     @OA\Items(
 *      @OA\Property(property="title", type="string", description="标题文字"),
 *      @OA\Property(property="content", type="string", description="内容文字"),
 *      @OA\Property(property="description", type="string", description="描述文字"),
 *      @OA\Property(property="language", type="string", description="语言"),
 *   ),description="多语言内容"),
 *   @OA\Property(property="show_type", type="integer", description="显示类型"),
 *   @OA\Property(property="display_show_type", type="string", description="显示类型显示"),
 *   @OA\Property(property="position", type="integer", description="位置"),
 *   @OA\Property(property="display_position", type="string", description="位置显示 banner:轮播图 top:顶部 bottom:底部"),
 *   @OA\Property(property="target_type", type="integer", description="跳转目标类型 1:不跳转 2:内部跳转 3:外部跳转"),
 *   @OA\Property(property="display_target_type", type="string", description="跳转目标类型显示"),
 *   @OA\Property(property="display_start_at", type="string", description="上架时间", format="date-time"),
 *   @OA\Property(property="display_end_at", type="string", description="下架时间", format="date-time"),
 *   @OA\Property(property="web_img_path", type="string", description="PC端图片"),
 *   @OA\Property(property="mobile_img_path", type="string", description="Mobile端图片"),
 *   @OA\Property(property="web_link_url", type="string", description="PC跳转地址"),
 *   @OA\Property(property="mobile_link_url", type="string", description="移动端跳转地址"),
 *   @OA\Property(property="status", type="boolean", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="状态显示"),
 *   @OA\Property(property="is_agent", type="boolean", description="是否是代理"),
 *   @OA\Property(property="display_is_agent", type="string", description="是否是代理显示"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 *   @OA\Property(property="admin_name", type="string", description="管理员"),
 * )
 */
class BannerTransformer extends Transformer
{
    public function transform(Banner $banner)
    {
        $data = [
            'id'                  => $banner->id,
            'currency'            => $banner->currency,
            'languages'           => $banner->languages,
            'code'                => $banner->code,
            'show_type'           => $banner->show_type,
            'display_show_type'   => transfer_show_value($banner->show_type, Banner::$showTypes),
            'position'            => $banner->position,
            'display_position'    => transfer_show_value($banner->position, Banner::$positions),
            'target_type'         => $banner->target_type,
            'display_target_type' => transfer_show_value($banner->target_type, Banner::$targetTypes),
            'display_start_at'    => convert_time($banner->display_start_at),
            'display_end_at'      => convert_time($banner->display_end_at),
            'web_img_path'        => strstr($banner->web_img_path, 'http') == false ? get_image_url($banner->web_img_path) : $banner->web_img_path,
            'mobile_img_path'     => strstr($banner->mobile_img_path, 'http') == false ? get_image_url($banner->mobile_img_path) : $banner->mobile_img_path,
            'web_link_url'        => $banner->web_link_url,
            'mobile_link_url'     => $banner->mobile_link_url,
            'sort'                => $banner->sort,
            'status'              => $banner->status,
            'display_status'      => transfer_show_value($banner->status, Model::$booleanStatusesDropList),
            'is_agent'            => $banner->is_agent,
            'display_is_agent'    => transfer_show_value($banner->is_agent, Model::$booleanDropList),
            'admin_name'          => $banner->admin_name,
        ];

        switch ($this->type) {
            case 'front_index':
                $languageSet         = $banner->getLanguageSet(app()->getLocale());
                $data['title']       = $languageSet['title'];
                $data['content']     = $languageSet['content'];
                $data['description'] = $languageSet['description'];
                $data                = collect($data)->only([
                    'id',
                    'title',
                    'description',
                    'button_text',
                    'content',
                    'target_type',
                    'web_img_path',
                    'mobile_img_path',
                    'web_link_url',
                    'mobile_link_url',
                    'display_position',
                ])->toArray();
                break;
        }
        return $data;
    }
}
