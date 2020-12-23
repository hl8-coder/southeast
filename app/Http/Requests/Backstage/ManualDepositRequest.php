<?php

namespace App\Http\Requests\Backstage;

use App\Http\Requests\Request;
use App\Models\PaymentPlatform;

class ManualDepositRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = [];
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
                        $data['payment_type']            = 'required';
                        $data['payment_platform_id']     = 'required|exists:payment_platforms,id';
                        $data['amount']                  = 'required|integer|min:0';
                        $data['online_banking_channel']  = 'required';
                        $data['company_bank_account_id'] = 'required|integer|exists:company_bank_accounts,id';
                        $data['deposit_date']            = 'required|date';
                        $data['user_bank_account_id']    = 'required|exists:user_bank_accounts,id';
                        $data['receipts']                = '';
                        $data['reference_id']            = '';
                        break;
                    # Rule 2: Over the Counter/Cash Deposit/Maunal
                    case PaymentPlatform::ONLINE_BANKING_CHANNEL_OVER_THE_COUNTER:
                    case PaymentPlatform::ONLINE_BANKING_CHANNEL_CASH_DEPOSIT:
                        $data['payment_type']            = 'required';
                        $data['payment_platform_id']     = 'required|exists:payment_platforms,id';
                        $data['amount']                  = 'required|integer|min:0';
                        $data['online_banking_channel']  = 'required';
                        $data['company_bank_account_id'] = 'required|integer|exists:company_bank_accounts,id';
                        $data['deposit_date']            = 'required|date';
                        $data['user_bank_account_name']  = 'required';
                        $data['user_bank_id']            = 'required|exists:banks,id';
                        $data['receipts']                = '';
                        $data['reference_id']            = 'required';
                        break;
                    default:
                        $data['online_banking_channel'] = 'required|numeric';
                        break;
                }

                break;
            # SCRATCH_CARD
            case PaymentPlatform::PAYMENT_TYPE_SCRATCH_CARD:
                $data['payment_type']        = 'required';
                $data['payment_platform_id'] = 'required|exists:payment_platforms,id';
                $data['amount']              = 'required|integer|min:0';
                $data['card_type']           = 'required';
                $data['pin_number']          = 'required';
                $data['serial_number']       = 'required';
                break;
            case PaymentPlatform::PAYMENT_TYPE_MPAY:
                    $data['payment_type']        = 'required';
                    $data['payment_platform_id'] = 'required|exists:payment_platforms,id';
                    $data['amount']              = 'required|integer|min:0';
                    $data['user_mpay_number']    = 'required';
                    $data['mpay_trading_code']   = 'required';
                break;
            case PaymentPlatform::PAYMENT_TYPE_LINEPAY:
                $data['payment_type']           = 'required';
                $data['payment_platform_id']    = 'required|exists:payment_platforms,id';
                $data['linepay_id']             = 'required';
                $data['deposit_date']           = 'required|date';
                $data['amount']                 = 'required|integer|min:0';
                break;
            default:
                $data['payment_type'] = 'required|numeric';
                break;
        }
        $data['user_name'] = 'required|exists:users,name';
        $data['is_agent']  = 'nullable|boolean';
        return $data;
    }
}
