<?php


namespace App\Transformers;


use App\Models\ContactInformation;
use App\Models\Model;

/**
 * @OA\Schema(
 *   schema="ContactInformation",
 *   type="object",
 *   @OA\Property(property="icon_id", type="integer", description="icon图片id"),
 *   @OA\Property(property="api_url", type="string", description="备注"),
 *   @OA\Property(property="is_enable", type="boolean", description="是否启用"),
 *   @OA\Property(property="is_affiliate", type="boolean", description="是否是代理平台"),
 *   @OA\Property(property="currency", type="string", description="币别显示"),
 *   @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
 *   )),
 *   @OA\Property(property="languages", type="array", description="语言", @OA\Items(
 *       @OA\Property(property="language", type="string", description="语言"),
 *       @OA\Property(property="title", type="string", description="标题"),
 *       @OA\Property(property="content", type="string", description="内容"),
 *   )),
 *   @OA\Property(property="title", type="string", description="标题"),
 *   @OA\Property(property="content", type="string", description="内容"),
 * )
 */
class ContactInformationTransformer extends Transformer
{
    public function transform(ContactInformation $information)
    {
        $data = [
            'id'                   => $information->id,
            'currencies'           => $information->currencies,
            'currency'             => implode(',', $information->currencies),
            'languages'            => $information->languages,
            'icon'                 => strstr($information->icon, 'http') == false ? get_image_url($information->icon) : $information->icon,
            'is_affiliate'         => $information->is_affiliate,
            'display_is_affiliate' => transfer_show_value($information->is_affiliate, Model::$booleanDropList),
            'is_enable'            => $information->is_enable,
            'display_is_enable'    => transfer_show_value($information->is_enable, Model::$booleanDropList),
            'api_url'              => $information->api_url,
            'sort'                 => $information->sort,
        ];
        switch ($this->type) {
            case 'front_index':
                $languageSet     = $information->getLanguageSet(app()->getLocale());
                $data['title']   = $languageSet['title'];
                $data['content'] = $languageSet['content'];
                break;
        }
        return $data;
    }
}