<?php


namespace App\Transformers;


use App\Models\AffiliateLink;
use App\Models\GamePlatformProduct;

/**
 * @OA\Schema(
 *   schema="AffiliateLink",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="ID"),
 *   @OA\Property(property="type", type="integer", description="类型"),
 *   @OA\Property(property="platform", type="integer", description="平台"),
 *   @OA\Property(property="languages", type="array", description="多语言",
 *     @OA\Items(
 *      @OA\Property(property="title", type="string", description="标题文字"),
 *      @OA\Property(property="language", type="string", description="语言"),
 *   ),description="多语言内容"),
 *   @OA\Property(property="currencies", type="array", description="币别", @OA\Items()),
 *   @OA\Property(property="title", type="string", description="标题"),
 *   @OA\Property(property="created_at", type="string", description="时间", format="date-time"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 *   @OA\Property(property="admin_name", type="string", description="管理员"),
 * )
 */
class AffiliateLinkTransformer extends Transformer
{
    public function transform(AffiliateLink $link)
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

        $linkUrl = $link->link;
        if ($linkUrl){
            $url = parse_url($linkUrl);
            preg_match_all('#('.implode('|', ['/vn/', '/en/', '/th/']).')#', $url['path'], $wordsFound);
            $wordsFound = array_unique($wordsFound[0]);
            if (count($wordsFound) <= 0){
                $linkUrl = str_replace($url['path'], "/{$language}" . $url['path'], $linkUrl);
            }
        }

        $data                     = [];
        $data['id']               = $link->id;
        $data['type']             = $link->type;
        $data['display_type']     = transfer_show_value($link->type, AffiliateLink::$type);
        $data['platform']         = $link->platform;
        $data['display_platform'] = transfer_show_value($link->platform, AffiliateLink::$platform);
        $data['status']           = $link->status;
        $data['link']             = $linkUrl;
        $data['sort']             = $link->sort;
        $data['admin_name']       = $link->admin_name;
        $data['display_status']   = transfer_show_value($link->status, AffiliateLink::$status);
        $data['currencies']       = $link->currencies;
        $data['languages']        = $link->languages;
        $data['created_at']       = convert_time($link->created_at);
        switch ($this->type) {
            case 'front_index':
                $languageSet   = $link->getLanguageSet(app()->getLocale());
                $data['title'] = $languageSet['title'];
                break;
        }
        return $data;
    }
}
