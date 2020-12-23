<?php

namespace App\Transformers;

use App\Models\GamePlatform;
use App\Models\Model;

/**
 * @OA\Schema(
 *   schema="GamePlatform",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="name", type="string", description="名称"),
 *   @OA\Property(property="code", type="string", description="唯一码"),
 *   @OA\Property(property="request_url", type="string", description="api请求地址"),
 *   @OA\Property(property="report_request_url", type="string", description="报表请求地址"),
 *   @OA\Property(property="launcher_request_url", type="string", description="游戏启动地址"),
 *   @OA\Property(property="rsa_our_private_key", type="string", description="RSA我方私钥"),
 *   @OA\Property(property="rsa_our_public_key", type="string", description="RSA我方公钥"),
 *   @OA\Property(property="rsa_public_key", type="string", description="RSA平台公钥"),
 *   @OA\Property(property="account", type="object", description="账户相关"),
 *   @OA\Property(property="interval", type="integer", description="间隔时间(分钟)"),
 *   @OA\Property(property="is_maintain", type="boolean", description="游戏是否维护"),
 *   @OA\Property(property="is_wallet_maintain", type="boolean", description="钱包是否维护"),
 *   @OA\Property(property="delay", type="integer", description="延迟时间(分钟)"),
 *   @OA\Property(property="offset", type="integer", description="偏移时间(分钟)"),
 *   @OA\Property(property="limit", type="integer", description="每分钟现在拉取几次"),
 *   @OA\Property(property="is_update_odds", type="boolean", description="是否更新odds"),
 *   @OA\Property(property="is_auto_transfer", type="boolean", description="是否支持自动转账"),
 *   @OA\Property(property="remark", type="string", description="抽成信息"),
 *   @OA\Property(property="status", type="boolean", description="状态"),
 *   @OA\Property(property="sort", type="integer", description="排序字段"),
 * )
 */
class GamePlatformTransformer extends Transformer
{
    public function transform(GamePlatform $platform)
    {
        $data = [
            'id'                   => $platform->id,
            'name'                 => $platform->name,
            'code'                 => $platform->code,
            'icon'                 => $platform->icon,
            'request_url'          => $platform->request_url,
            'report_request_url'   => $platform->report_request_url,
            'launcher_request_url' => $platform->launcher_request_url,
            'rsa_our_private_key'  => $platform->rsa_our_private_key,
            'rsa_our_public_key'   => $platform->rsa_our_public_key,
            'rsa_public_key'       => $platform->rsa_public_key,
            'account'              => $platform->account,
            'interval'             => $platform->interval,
            'delay'                => $platform->delay,
            'offset'               => $platform->offset,
            'is_maintain'          => $platform->is_maintain,
            'is_wallet_maintain'   => $platform->is_wallet_maintain,
            'is_update_odds'       => $platform->is_update_odds,
            'limit'                => $platform->limit,
            'is_auto_transfer'     => transfer_show_value($platform->is_auto_transfer, Model::$booleanDropList),
            'remark'               => $platform->remark,
            'sort'                 => $platform->sort,
            'is_rebate'            => transfer_show_value($platform->is_rebate, Model::$booleanDropList),
            'is_bonus'             => transfer_show_value($platform->is_bonus, Model::$booleanDropList),
            'status'               => $platform->status,
            'display_status'       => transfer_show_value($platform->status, Model::$booleanStatusesDropList),
        ];

        switch ($this->type) {
            default:
                return $data;
            case 'index':
                return collect($data)->only([
                                                'id',
                                                'name',
                                                'code',
                                                'is_web_show',
                                                'is_mobile_show',
                                                'is_auto_transfer',
                                                'is_maintain',
                                                'is_wallet_maintain',
                                                'remark',
                                                'status',
                                                'display_status',
                                                'sort',
                                            ])->toArray();
                break;
            case 'associate':
                return collect($data)->only(['id', 'name', 'code'])->toArray();
                break;
        }
    }
}
