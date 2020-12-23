<?php
namespace App\Transformers;

use App\Models\AffiliateCommission;
use Carbon\Carbon;

class CommissionsPayoutDownloadTransformer extends Transformer
{
    public function transform(AffiliateCommission $commission)
    {
        return [
            'Currency'              => $commission->currency,
            'Affiliate ID'          => $commission->user_name,
            'Account Name'          => $commission->account_name,
            'Payout Comm'           => thousands_number($commission->payout_commission),
            'Bank Account'          => $commission->account_no,
            'Bank Address'          => $commission->province . ' ' . $commission->city,
            'Status'                => transfer_show_value($commission->status, AffiliateCommission::$statuses),
        ];
    }
}