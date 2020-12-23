<?php

namespace App\Transformers;

use App\Models\Model;
use App\Models\PaymentPlatform;
use App\Models\PgAccount;

/**
 * @OA\Schema(
 *   schema="PaymentPlatform",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="支付平台id"),
 *   @OA\Property(property="name", type="string", description="名称"),
 *   @OA\Property(property="code", type="string", description="辨识码"),
 *   @OA\Property(property="remarks", type="string", description="备注"),
 *   @OA\Property(property="currencies", type="string", description="可用币别"),
 *   @OA\Property(property="currency", type="string", description="币别显示"),
 *   @OA\Property(property="devices", type="string", description="可用装置"),
 *   @OA\Property(property="payment_type", type="integer", description="支付类型"),
 *   @OA\Property(property="customer_id", type="string", description="商户id"),
 *   @OA\Property(property="customer_key", type="string", description="商户私钥"),
 *   @OA\Property(property="request_url", type="string", description="请求地址"),
 *   @OA\Property(property="request_type", type="integer", description="请求类型"),
 *   @OA\Property(property="related_name", type="string", description="关联名称"),
 *   @OA\Property(property="related_no", type="string", description="关联号码"),
 *   @OA\Property(property="max_deposit", type="integer", description="单笔最大充值金额"),
 *   @OA\Property(property="min_deposit", type="integer", description="单笔最低充值金额"),
 *   @OA\Property(property="max_fee", type="integer", description="最大手续费"),
 *   @OA\Property(property="min_fee", type="integer", description="最小手续费"),
 *   @OA\Property(property="is_need_type_amount", type="boolean", description="是否需要输入金额"),
 *   @OA\Property(property="is_fee", type="boolean", description="是否需要手续费"),
 *   @OA\Property(property="display_is_fee", type="string", description="显示是否需要手续费"),
 *   @OA\Property(property="fee_rebate", type="integer", description="充值手续费百分比"),
 *   @OA\Property(property="image_path", type="string", description="图片地址"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="显示状态"),
 *   @OA\Property(property="balance", type="string", description="通道余额"),
 *   @OA\Property(property="username", type="string", description="第三方通道登陆用户username"),
 *   @OA\Property(property="password", type="string", description="第三方通道登陆用户password"),
 *   @OA\Property(property="email", type="string", description="邮箱"),
 *   @OA\Property(property="email_password", type="string", description="邮箱密码"),
 *   @OA\Property(property="otp", type="integer", description="otp"),
 *   @OA\Property(property="display_otp", type="string", description="otp名称"),
 * )
 */
class PaymentPlatformTransformer extends Transformer
{
    public function transform(PaymentPlatform $paymentPlatform)
    {
        $data =  [
            'id'                  => $paymentPlatform->id,
            'name'                => $paymentPlatform->name,
            'code'                => $paymentPlatform->code,
            'remarks'             => $paymentPlatform->remarks,
            'currencies'          => explode(",", $paymentPlatform->currencies),
            'currency'            => $paymentPlatform->currencies,
            'devices'             => $paymentPlatform->devices,
            'payment_type'        => $paymentPlatform->payment_type,
            'customer_id'         => $paymentPlatform->customer_id,
            'customer_key'        => $paymentPlatform->customer_key,
            'request_url'         => $paymentPlatform->request_url,
            'request_type'        => $paymentPlatform->request_type,
            'related_name'        => $paymentPlatform->related_name,
            'related_no'          => $paymentPlatform->related_no,
            'max_deposit'         => $paymentPlatform->max_deposit,
            'min_deposit'         => $paymentPlatform->min_deposit,
            'is_fee'              => $paymentPlatform->is_fee,
            'display_is_fee'      => transfer_show_value($paymentPlatform->is_fee, Model::$booleanDropList),
            'max_fee'             => $paymentPlatform->max_fee,
            'min_fee'             => $paymentPlatform->min_fee,
            'is_need_type_amount' => $paymentPlatform->is_need_type_amount,
            'show_type'           => $paymentPlatform->show_type,
            'display_show_type'   => transfer_show_value($paymentPlatform->show_type, PaymentPlatform::$showTypes),
            'fee_rebate'          => $paymentPlatform->fee_rebate,
            'image_path'          => empty($paymentPlatform->image_path) ? null : get_image_url($paymentPlatform->image_path),
            'sort'                => $paymentPlatform->sort,
            'status'              => $paymentPlatform->status,
            'display_status'      => transfer_show_value($paymentPlatform->status, Model::$booleanStatusesDropList),
        ];

        switch ($this->type) {
            case "pg":
                $data['balance'] = !empty($paymentPlatform->pgAccount) ? thousands_number($paymentPlatform->pgAccount->current_balance) : 0;
                $data['username'] = !empty($paymentPlatform->pgAccount) ? $paymentPlatform->pgAccount->username : '';
                $data['password'] = !empty($paymentPlatform->pgAccount) ? $paymentPlatform->pgAccount->password : '';
                $data['email'] = !empty($paymentPlatform->pgAccount) ? $paymentPlatform->pgAccount->email : '';
                $data['email_password'] = !empty($paymentPlatform->pgAccount) ? $paymentPlatform->pgAccount->email_password : '';
                $data['otp']        = !empty($paymentPlatform->pgAccount) ? $paymentPlatform->pgAccount->otp : '';
                $data['display_otp']        = !empty($paymentPlatform->pgAccount) ? transfer_show_value($paymentPlatform->pgAccount->otp,PgAccount::$otps) : '';
                break;
            default:
                break;
        }


        return $data;
    }
}
