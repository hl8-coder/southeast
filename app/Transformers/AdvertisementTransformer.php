<?php
namespace App\Transformers;

use App\Models\Advertisement;

/**
 * @OA\Schema(
 *   schema="Advertisement",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="ID"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="web_img_path", type="string", description="PC端图片"),
 *   @OA\Property(property="mobile_img_path", type="string", description="移动端图片"),
 *   @OA\Property(property="login_img_path", type="string", description="登录页图片"),
 *   @OA\Property(property="description", type="string", description="图片描述"),
 *   @OA\Property(property="img_link_url", type="string", description="图片跳转地址"),
 *   @OA\Property(property="alone_link_url", type="string", description="独立跳转地址"),
 *   @OA\Property(property="target_type", type="integer", description="链接打开方式"),
 *   @OA\Property(property="show_type", type="integer", description="显示类型"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */

class AdvertisementTransformer extends Transformer
{
    public function transform(Advertisement $advertisement)
    {
        return [
            'id'                => $advertisement->id,
            'currency'          => $advertisement->currency,
            'web_img_path'      => get_image_url($advertisement->web_img_path),
            'mobile_img_path'   => get_image_url($advertisement->mobile_img_path),
            'login_img_path'    => get_image_url($advertisement->login_img_path),
            'description'       => $advertisement->description,
            'img_link_url'      => $advertisement->img_link_url,
            'alone_link_url'    => $advertisement->alone_link_url,
            'target_type'       => transfer_show_value($advertisement->target_type, Advertisement::$targetTypes),
            'show_type'         => transfer_show_value($advertisement->show_type, Advertisement::$showTypes),
            'sort'              => $advertisement->sort,
            'created_at'        => convert_time($advertisement->created_at),
        ];
    }
}