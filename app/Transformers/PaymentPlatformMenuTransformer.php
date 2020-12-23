<?php
namespace App\Transformers;

use App\Models\Model;
use App\Models\PaymentPlatform;

/**
 * @OA\Schema(
 *   schema="PaymentPlatformMenu",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="支付平台id"),
 *   @OA\Property(property="payment_type", type="integer", description="支付类型"),
 *   @OA\Property(property="name", type="string", description="名称"),
 * )
 */
class PaymentPlatformMenuTransformer extends Transformer
{

    public function transform(PaymentPlatform $paymentPlatform)
    {
        $id = $paymentPlatform->payment_type == PaymentPlatform::PAYMENT_TYPE_BANKCARD ? '' : $paymentPlatform->id;
        switch ($paymentPlatform->payment_type){
            case PaymentPlatform::PAYMENT_TYPE_BANKCARD:
                $name = __('paymentPlatform.internet_banking');
                break;
            case PaymentPlatform::PAYMENT_TYPE_SCRATCH_CARD:
                $name = __('paymentPlatform.scratch') .' '. $paymentPlatform->name;
                break;
            default:
                $name = $paymentPlatform->name;
        }

        return [
            'id'            => $id,
            'payment_type'  => $paymentPlatform->payment_type,
            'name'          => $name,
        ];
    }
}
