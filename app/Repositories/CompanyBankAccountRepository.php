<?php
namespace App\Repositories;

use App\Models\CompanyBankAccount;
use App\Models\PaymentGroup;

class CompanyBankAccountRepository
{

    public static function getDepositAccounts($paymentGroupId)
    {
        return CompanyBankAccount::query()
            ->where('payment_group_id', $paymentGroupId)
            ->where('type', CompanyBankAccount::TYPE_DEPOSIT)
            ->where('status', true)
            ->get();
    }

    public static function findByCode($code)
    {
        return CompanyBankAccount::query()->where('code', $code)->first();
    }

    /**
     * 转换audit中的值
     *
     * @param  string   $field  字段
     * @param  string   $value  值
     * @return string
     */
    public static function transformAudit($field, $value)
    {
        $result = $value;

        switch ($field) {
            case 'type':
                $result = CompanyBankAccount::$types[$value];
                break;
            case 'status':
                $result = CompanyBankAccount::$statuses[$value];
                break;
            case 'payment_group_id':
                $paymentGroup = PaymentGroup::findByCache($value);
                $result = $paymentGroup ? $paymentGroup->name : '';
                break;
            case 'otp':
                $result = isset(CompanyBankAccount::$otps[$value]) ? CompanyBankAccount::$otps[$value] : '';
                break;
            case 'app_related':
                $result = isset(CompanyBankAccount::$appRelates[$value]) ? CompanyBankAccount::$appRelates[$value] : '';
                break;
        }

        return $result;
    }
}