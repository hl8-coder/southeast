<?php


namespace App\Transformers;


use App\Models\CreativeResource;

class CreativeResourceTransformer extends Transformer
{
    /**
     * @OA\Schema(
     *   schema="CreativeResource",
     *   type="object",
     *   @OA\Property(property="id", type="integer", description="id"),
     *   @OA\Property(property="code", type="string", description="唯一code"),
     *   @OA\Property(property="type", type="integer", description="资源类型"),
     *   @OA\Property(property="display_type", type="string", description="资源类型"),
     *   @OA\Property(property="group", type="integer", description="资源分组"),
     *   @OA\Property(property="display_group", type="string", description="资源分组"),
     *   @OA\Property(property="size", type="integer", description="图片尺寸"),
     *   @OA\Property(property="display_size", type="string", description="图片尺寸"),
     *   @OA\Property(property="tracking_id", type="integer", description="名称ID"),
     *   @OA\Property(property="tracking_name", type="string", description="名称"),
     *   @OA\Property(property="banner_path", type="string", description="资源地址"),
     *   @OA\Property(property="banner_url", type="string", description="资源链接"),
     *   @OA\Property(property="currency", type="string", description="币别"),
     *   @OA\Property(property="last_update_by", type="string", description="上次更新"),
     * )
     */
    public function transform(CreativeResource $resource)
    {
        $language = app()->getLocale();
        switch ($language){
            case 'vi-VN':
                $language = 'vn';
                break;
            case 'en-US':
                $language = 'en';
                break;
        }
        
        $bannerUrl = $resource->banner_url;
        if ($bannerUrl){
            $url = parse_url($bannerUrl);
            preg_match_all('#('.implode('|', ['/vn/', '/en/', '/th/']).')#', $url['path'], $wordsFound);
            $wordsFound = array_unique($wordsFound[0]);
            if (count($wordsFound) <= 0){
                $bannerUrl = str_replace($url['path'], "/{$language}" . $url['path'], $bannerUrl);
            }
        }

        return [
            'id'             => $resource->id,
            'code'           => $resource->code,
            'type'           => (int)$resource->type,
            'display_type'   => transfer_show_value($resource->type, CreativeResource::$type),
            'group'          => (int)$resource->group,
            'display_group'  => transfer_show_value($resource->group, CreativeResource::$group),
            'size'           => (int)$resource->size,
            'display_size'   => transfer_show_value($resource->size, CreativeResource::$size),
            'currency'       => $resource->currency,
            'banner_path'    => get_image_url($resource->banner_path),
            'banner_url'     => $bannerUrl,
            'last_update_by' => $resource->last_update_by,
            'created_at'     => convert_time($resource->created_at),
        ];
    }
}
