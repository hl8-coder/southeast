<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\RiskGroup;
use App\Models\UserInfo;
use App\Repositories\UserRepository;

/**
 * @OA\Schema(
 *   schema="UserInfo",
 *   type="object",
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="full_name", type="string", description="真实姓名"),
 *   @OA\Property(property="gender", type="string", description="性别(male|female)", example="male"),
 *   @OA\Property(property="address", type="string", description="地址"),
 *   @OA\Property(property="email", type="string", description="邮件"),
 *   @OA\Property(property="is_email_verified", type="boolean", description="邮箱是否已验证"),
 *   @OA\Property(property="country_code", type="string", description="电话国际编码"),
 *   @OA\Property(property="phone", type="string", description="电话号码"),
 *   @OA\Property(property="avatar", type="string", description="头像地址"),
 *   @OA\Property(property="describe", type="string", description="自我描述"),
 *   @OA\Property(property="is_phone_verified", type="boolean", description="电话是否已验证"),
 *   @OA\Property(property="birth_at", type="string", format="date-time", description="生日"),
 *   @OA\Property(property="register_url", type="string", description="注册url"),
 *   @OA\Property(property="register_ip", type="string", description="注册ip"),
 *   @OA\Property(property="last_login_ip", type="string", description="最后登录ip"),
 *   @OA\Property(property="last_login_at", type="string", format="date-time", description="最后登录时间"),
 *   @OA\Property(property="is_profile_verified", type="boolean", description="是否验证信息"),
 *   @OA\Property(property="is_bank_account_verified", type="boolean", description="是否验证公司银行卡"),
 *   @OA\Property(property="verified_percent", type="number", description="验证完成百分比"),
 *   @OA\Property(property="is_can_claim_verify_prize", type="boolean", description="是否可以领取验证奖励"),
 * )
 */
class UserInfoTransformer extends Transformer
{
    public function transform(UserInfo $userInfo)
    {
        $verifiedCount = UserRepository::findVerifiedCount($userInfo);

        $gender = $userInfo->gender;

        if ($gender) {
            $gender = transfer_lang_value('dropList', UserInfo::$gendersForTranslation)[$gender];
        }

        $data = [
            'user_id'                   => $userInfo->user_id,
            'full_name'                 => $userInfo->full_name,
            'gender'                    => $gender,
            'address'                   => $userInfo->address,
            'email'                     => $userInfo->email,
            'is_email_verified'         => !is_null($userInfo->email_verified_at),
            'country_code'              => $userInfo->country_code,
            'phone'                     => $userInfo->phone,
            'is_phone_verified'         => !is_null($userInfo->phone_verified_at),
            'birth_at'                  => $userInfo->birth_at,
            'avatar'                    => get_image_url($userInfo->avatar),
            'describe'                  => $userInfo->describe,
            'register_url'              => $userInfo->register_url,
            'register_ip'               => $userInfo->register_ip,
            'city'                      => $userInfo->city,
            'web_url'                   => $userInfo->web_url,
            'display_device'            => transfer_show_value($userInfo->last_device, User::$devices),
            'last_login_ip'             => $userInfo->last_login_ip,
            'display_gender'            => $userInfo->gender,
            'last_login_at'             => convert_time($userInfo->last_login_at),
            'is_profile_verified'       => !is_null($userInfo->profile_verified_at),
            'is_bank_account_verified'  => !is_null($userInfo->bank_account_verified_at),
            'verified_percent'          => ($verifiedCount / 4) * 100,
            'is_can_claim_verify_prize' => UserRepository::isCanClaimVerifyPrize($userInfo),
        ];

        switch ($this->type){
            case 'front_index':
                $languageSet = collect($data['describe'])->where('language', app()->getLocale())->first();
                $data['describe'] = empty($languageSet) ? '' : $languageSet['content'];
                return collect($data)->only(['avatar', 'describe'])->toArray();
                break;
            case 'front_show':
                $rules = $this->data ?? [];
                if (in_array(RiskGroup::RULE_NO_ACCOUNT_SAFETY_BONUS, $rules)){
                    $data['is_can_claim_verify_prize'] = false;
                }
                break;
        }
        return $data;
    }
}
