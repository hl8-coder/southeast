<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\UserLoginLog;

/**
 * @OA\Schema(
 *   schema="UserLoginLog",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="user_name", type="string", description="会员名称"),
 *   @OA\Property(property="device", type="integer", description="装置"),
 *   @OA\Property(property="display_device", type="string", description="装置显示"),
 *   @OA\Property(property="equipment", type="string", description="设备"),
 *   @OA\Property(property="browser", type="string", description="浏览器信息"),
 *   @OA\Property(property="ip", type="string", description="IP"),
 *   @OA\Property(property="country", type="string", description="国家"),
 *   @OA\Property(property="city", type="string", description="省"),
 *   @OA\Property(property="state", type="string", description="地区"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="created_at", type="string", description="登录时间"),
 *   @OA\Property(property="user",  description="会员", ref="#/components/schemas/User"),
 * )
 */
class UserLoginLogTransformer extends Transformer
{
    protected $availableIncludes = ['user'];

    public function transform(UserLoginLog $log)
    {
        $parent_name = '-';
        if ($log->user->parentUser) {
            $parent_name = $log->user->parentUser->name;
        }
        $data = [
            'id'             => $log->id,
            'user_id'        => $log->user_id,
            'user_name'      => $log->user_name,
            'parent_name'    => $parent_name,
            'device'         => $log->device,
            'display_device' => transfer_show_value($log->device, User::$devices),
            'equipment'      => $log->equipment,
            'browser'        => $log->browser,
            'ip'             => $log->ip,
            'country'        => $log->country,
            'city'           => $log->city,
            'state'          => $log->state,
            'remark'         => $log->remark,
            'status'         => transfer_show_value($log->success_login, UserLoginLog::$loginStatus),
            'created_at'     => convert_time($log->created_at),
            'login_time'     => convert_time($log->created_at),
            'login_date'     => date("Y-m-d", strtotime($log->created_at)),
            'currency'       => $log->user->currency,
        ];
        switch ($this->type) {
            case 'backstage_index':
                $uuc         = UserLoginLog::query()
                    ->where('ip', $log->ip)
                    ->whereHas('user', function ($query) {
                        $query->where('is_agent', false);
                    })
                    ->select('user_name')
                    ->groupBy('user_name')
                    ->get();;
                $data['uuc'] = $uuc->count();
                break;
            case 'backstage_index_by_ip':
                $uuc         = UserLoginLog::query()
                    ->where([
                        [
                            'ip', $log->ip
                        ],
                        [
                            'user_name', $log->user_name
                        ]
                    ])
                    ->whereHas('user', function ($query) {
                        $query->where('is_agent', false);
                    })
                    ->groupBy('user_name')
                    ->count();
                $data['uuc'] = $uuc;
                break;
        }
        return $data;
    }

    public function includeUser(UserLoginLog $log)
    {
        return $this->item($log->user, new UserTransformer());
    }
}