<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\Currency;
use App\Models\PaymentPlatform;
use App\Models\User;

class PaymentPlatformRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'name'         => 'required|string',
                    'display_name' => 'required|string',
                    'currencies'   => 'required',
                    'code'         => 'required|string|unique:payment_platforms',

                    'payment_type' => 'required|integer|in:' . implode(',', array_keys(PaymentPlatform::$paymentTypes)),
                    'request_type' => 'required|integer|in:' . implode(',', array_keys(PaymentPlatform::$requestTypes)),
                    'request_url'  => 'required_if:payment_type,' . PaymentPlatform::PAYMENT_TYPE_QUICKPAY . '|required_if:payment_type,' . PaymentPlatform::PAYMENT_TYPE_SCRATCH_CARD,

                    'devices'             => 'nullable|array',
                    'devices.*'           => 'nullable|string|in:' . get_validate_in_string(User::$devices),
                    'customer_id'         => 'nullable|string',
                    'customer_key'        => 'nullable|string',
                    'related_no'          => 'nullable|string',
                    'max_deposit'         => 'nullable|numeric|min:0',
                    'min_deposit'         => 'nullable|numeric|min:0|lte:max_deposit',
                    'is_fee'              => 'nullable|boolean',
                    'fee_rebate'          => 'nullable|numeric|min:0|max:100',
                    'max_fee'             => 'nullable|numeric|min:0',
                    'min_fee'             => 'nullable|numeric|min:0|lte:max_fee',
                    'show_type'           => 'nullable|in:' . get_validate_in_string(PaymentPlatform::$showTypes),
                    'sort'                => 'nullable|integer|min:0',
                    'status'              => 'nullable|integer',
                    'image_id'            => 'nullable|integer|exists:images,id',
                    'remarks'             => 'nullable|string',
                    'is_need_type_amount' => 'nullable|integer|exists:images,id',
                ];
                break;
            case 'PATCH':
                return [
                    'name'                => 'nullable|string',
                    'devices'             => 'nullable|array',
                    'devices.*'           => 'nullable|string|in:' . get_validate_in_string(User::$devices),
                    'currencies'          => 'nullable',
                    'payment_type'        => 'nullable|integer|in:' . get_validate_in_string(PaymentPlatform::$paymentTypes),
                    'customer_id'         => 'nullable|string',
                    'customer_key'        => 'nullable|string',
                    'request_url'         => 'nullable|string',
                    'request_type'        => 'nullable|integer|in:' . get_validate_in_string(PaymentPlatform::$requestTypes),
                    'is_need_type_amount' => 'nullable|integer',
                    'max_deposit'         => 'nullable|numeric|min:0',
                    'min_deposit'         => 'nullable|numeric|min:0|lte:max_deposit',
                    'is_fee'              => 'nullable|boolean',
                    'fee_rebate'          => 'nullable|numeric|min:0',
                    'related_no'          => 'nullable|string',
                    'max_fee'             => 'nullable|numeric|min:0',
                    'min_fee'             => 'nullable|numeric|min:0|lte:max_fee',
                    'show_type'           => 'nullable|in:' . get_validate_in_string(PaymentPlatform::$showTypes),
                    'sort'                => 'nullable|integer|min:0',
                    'status'              => 'nullable|integer',
                    'image_id'            => 'nullable|integer',
                ];
                break;
        }

        return [];
    }
}
