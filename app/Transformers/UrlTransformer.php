<?php


namespace App\Transformers;


use App\Models\Model;
use App\Models\Url;
use App\Models\User;

/**
 * @OA\Schema(
 *   schema="Urls",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="URLid"),
 *   @OA\Property(property="type", type="integer", description="类型"),
 *   @OA\Property(property="display_type", type="string", description="类型"),
 *   @OA\Property(property="address", type="string", description="域名"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="状态"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="remaupdate_byrk", type="string", description="修改者"),
 * )
 */
class UrlTransformer extends Transformer
{
    public function transform(Url $url)
    {
        return [
            'id'               => $url->id,
            'type'             => $url->type,
            'display_type'     => transfer_show_value($url->type, Url::$type),
            'device'           => $url->device,
            'display_device'   => transfer_show_value($url->device, User::$devices),
            'platform'         => $url->device,
            'display_platform' => transfer_show_value($url->platform, Url::$platform),
            'address'          => $url->address,
            'currencies'       => $url->currencies,
            'status'           => $url->status,
            'display_status'   => transfer_show_value($url->status, Model::$booleanStatusesDropList),
            'remark'           => $url->remark,
            'update_by'        => $url->update_by,
        ];
    }
}