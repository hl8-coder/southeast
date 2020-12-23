<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;
use App\Models\Deposit;
use App\Models\PaymentPlatform;

class DepositRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        # 支付类型
        switch ($this->input("payment_type")) {
            # online banking
            case PaymentPlatform::PAYMENT_TYPE_BANKCARD:
                # Online Banking 类型
                switch ($this->input("online_banking_channel")) {
                    # Rule 1: ATM/Internet Banking/Mobile Banking
                    case PaymentPlatform::ONLINE_BANKING_CHANNEL_ATM:
                    case PaymentPlatform::ONLINE_BANKING_CHANNEL_INTERNET_BANKING:
                    case PaymentPlatform::ONLINE_BANKING_CHANNEL_MOBILE_BANKING:
                        return [
                            'payment_type'            => 'required',
                            'payment_platform_id'     => 'required|exists:payment_platforms,id',
                            'amount'                  => 'required|integer|min:0',
                            'online_banking_channel'  => 'required',
                            'company_bank_account_id' => 'required|integer|exists:company_bank_accounts,id',
                            'deposit_date'            => 'required|date',
                            'user_bank_account_id'    => 'required|exists:user_bank_accounts,id',
                            'receipts'                => '',
                            'reference_id'            => '',
                        ];
                        break;
                    # Rule 2: Over the Counter/Cash Deposit/Maunal
                    case PaymentPlatform::ONLINE_BANKING_CHANNEL_OVER_THE_COUNTER:
                    case PaymentPlatform::ONLINE_BANKING_CHANNEL_CASH_DEPOSIT:
                        return [
                            'payment_type'            => 'required',
                            'payment_platform_id'     => 'required|exists:payment_platforms,id',
                            'amount'                  => 'required|integer|min:0',
                            'online_banking_channel'  => 'required',
                            'company_bank_account_id' => 'required|integer|exists:company_bank_accounts,id',
                            'deposit_date'            => 'required|date',
                            'user_bank_account_name'  => 'required',
                            'user_bank_id'            => 'required|exists:banks,id',
                            'receipts'                => '',
                            'reference_id'            => 'required',
                        ];
                        break;
                    default:
                        return [
                            'online_banking_channel' => 'required|numeric',
                        ];
                        break;
                }

                break;
            # QuickPay
            case PaymentPlatform::PAYMENT_TYPE_QUICKPAY:
                return [
                    'payment_type'        => 'required',
                    'payment_platform_id' => 'required|exists:payment_platforms,id',
                    'amount'              => 'required|integer|min:0',
                    'bank_code'           => 'required',
                    'redirect'            => 'required',
                ];
                break;
            # Mpay
            case PaymentPlatform::PAYMENT_TYPE_MPAY:
                return [
                    'payment_type'        => 'required',
                    'payment_platform_id' => 'required|exists:payment_platforms,id',
                    'amount'              => 'required|integer|min:0',
                    'user_mpay_number'    => 'required',
                    'mpay_trading_code'   => 'required',
                ];
                break;
            # SCRATCH_CARD
            case PaymentPlatform::PAYMENT_TYPE_SCRATCH_CARD:
                return [
                    'payment_type'        => 'required',
                    'payment_platform_id' => 'required|exists:payment_platforms,id',
                    'amount'              => 'integer|min:0',
                    'card_type'           => 'required',
                    'pin_number'          => 'required',
                    'serial_number'       => [
                        'required',
                        function ($attribute, $value, $fail) {
                            if (Deposit::query()->where('pin_number', $this->pin_number)->where('serial_number', $value)->exists()) {
                                $fail(__('deposit.already_used'));
                            }
                        },
                    ],
                ];
                break;
            default:
                return [
                    'payment_type' => 'required|numeric',
                ];
                break;
        }
    }

    public function attributes()
    {
        return [
            'payment_type'            => __('request/api/deposit.payment_type'),
            'payment_platform_id'     => __('request/api/deposit.payment_platform_id'),
            'amount'                  => __('request/api/deposit.amount'),
            'online_banking_channel'  => __('request/api/deposit.online_banking_channel'),
            'company_bank_account_id' => __('request/api/deposit.company_bank_account_id'),
            'deposit_date'            => __('request/api/deposit.deposit_date'),
            'user_bank_account_id'    => __('request/api/deposit.user_bank_account_id'),
            'receipts'                => __('request/api/deposit.receipts'),
            'reference_id'            => __('request/api/deposit.reference_id'),
            'user_bank_account_name'  => __('request/api/deposit.user_bank_account_name'),
            'user_bank_id'            => __('request/api/deposit.user_bank_id'),
            'bank_code'               => __('request/api/deposit.bank_code'),
            'redirect'                => __('request/api/deposit.redirect'),
            'user_mpay_number'        => __('request/api/deposit.user_mpay_number'),
            'mpay_trading_code'       => __('request/api/deposit.mpay_trading_code'),
            'card_type'               => __('request/api/deposit.card_type'),
            'pin_number'              => __('request/api/deposit.pin_number'),
            'serial_number'           => __('request/api/deposit.serial_number'),
        ];
    }

    public function messages()
    {
        return [
            'amount.integer'  => __('request/api/deposit.deposit_integer'),
            'mpay_trading_code.required'  => __('request/api/deposit.mpay_trading_code_required'),
            'user_mpay_number.required'  => __('request/api/deposit.user_mpay_number_required'),
        ];
    }
}
