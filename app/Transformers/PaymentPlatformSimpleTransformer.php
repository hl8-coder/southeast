<?php

namespace App\Transformers;

use App\Models\Model;
use App\Models\PaymentPlatform;
use App\Models\UserMpayNumber;

/**
 * @OA\Schema(
 *   schema="PaymentPlatformSimple",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="支付平台id"),
 *   @OA\Property(property="name", type="string", description="名称"),
 *   @OA\Property(property="code", type="string", description="辨识码"),
 *   @OA\Property(property="currencies", type="string", description="可用币别"),
 *   @OA\Property(property="devices", type="string", description="可用装置"),
 *   @OA\Property(property="payment_type", type="integer", description="支付类型"),
 *   @OA\Property(property="related_name", type="string", description="关联名称"),
 *   @OA\Property(property="related_no", type="string", description="关联号码"),
 *   @OA\Property(property="user_mpay_numbers", type="array", description="会员多个mpay", @OA\Items(
 *      @OA\Property(property="country_code", type="string", description="mpay电话国家code"),
 *      @OA\Property(property="number", type="string", description="电话号码"),
 *   )),
 *   @OA\Property(property="max_deposit", type="integer", description="单次最大充值"),
 *   @OA\Property(property="min_deposit", type="integer", description="单次最小充值"),
 *   @OA\Property(property="image_path", type="string", description="图片地址"),
 *   @OA\Property(property="daily_limit", type="integer", description="每日充值量"),
 *   @OA\Property(property="total_allowed", type="integer", description="允许总充值量"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="is_need_type_amount", type="boolean", description="是否需要输入金额"),
 *   @OA\Property(property="companyBankAccount", ref="#/components/schemas/CompanyBankAccount"),
 * )
 */
class PaymentPlatformSimpleTransformer extends Transformer
{
    protected $availableIncludes = ['companyBankAccount'];

    public function transform(PaymentPlatform $paymentPlatform)
    {
        $data = [
            'id'                   => $paymentPlatform->id,
            'name'                 => $paymentPlatform->name,
            'code'                 => $paymentPlatform->code,
            'currencies'           => $paymentPlatform->currencies,
            'devices'              => $paymentPlatform->devices,
            'payment_type'         => $paymentPlatform->payment_type,
            'related_name'         => $paymentPlatform->related_name,
            'related_no'           => $paymentPlatform->related_no,
            'max_deposit'          => thousands_number($paymentPlatform->max_deposit),
            'min_deposit'          => thousands_number($paymentPlatform->min_deposit),
            'image_path'           => get_image_url($paymentPlatform->image_path),
            'country_code'         => '',
            'number'               => '',
            'daily_limit'          => 0,
            'total_allowed'        => 0,
            'sort'                 => $paymentPlatform->sort,
            'status'               => $paymentPlatform->status,
            'is_need_type_amount'  => $paymentPlatform->is_need_type_amount,
            'display_status'       => transfer_show_value($paymentPlatform->status, Model::$booleanStatusesDropList),
            'display_payment_type' => transfer_show_value($paymentPlatform->payment_type, PaymentPlatform::$paymentTypes),
        ];

        switch ($this->type) {
            case 'all':
                if (!empty($paymentPlatform->companyBankAccount)) {
                    $currencySet = $paymentPlatform->companyBankAccount->bank->getLanguageSet(app()->getLocale());
                    $data['name'] = $currencySet['front_name'];
                }
                break;
            case 'index':
                break;
            default:
                $banks     = [];
                $cardTypes = [];
                $codes     = explode('-', $paymentPlatform->code);
                $class     = 'App\\Payments\\' . ucwords(strtolower($codes[0]));

                $currency = isset($this->data['currency']) ? $this->data['currency'] : 'VND';

                if (class_exists($class)) {
                    if ($user = request()->user()) {
                        $currency = $user->currency;
                    }
                    $banks     = app($class)->getBankCodeList($paymentPlatform, $currency);
                    $cardTypes = app($class)->getCardTypeList($paymentPlatform, $currency);
                }

                $data['banks']      = transform_list($banks);
                $data['card_types'] = $cardTypes;

                if (PaymentPlatform::PAYMENT_TYPE_MPAY == $paymentPlatform->payment_type
                    && isset($this->data['user'])
                    && $userMpaies = UserMpayNumber::getActiveByUserId($this->data['user']->id)) {
                    foreach ($userMpaies as $key => $userMpay) {
                        $data['user_mpay_numbers'][$key]['country_code']   = $userMpay->area_code;
                        $data['user_mpay_numbers'][$key]['number']         = hidden_number($userMpay->number, 4);
                    }
                }

                break;
        }

        return $data;
    }

    public function includeCompanyBankAccount(PaymentPlatform $paymentPlatform)
    {
        $type = $this->type == 'front' ? $this->type : 'index';
        if ($paymentPlatform->companyBankAccount) {
            return $this->item($paymentPlatform->companyBankAccount, new CompanyBankAccountTransformer($type, $this->data));
        }
        return null;
    }
}
