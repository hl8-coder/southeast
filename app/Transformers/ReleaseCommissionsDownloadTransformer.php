<?php
namespace App\Transformers;

use App\Models\AffiliateCommission;
use Carbon\Carbon;

class ReleaseCommissionsDownloadTransformer extends Transformer
{
    public function transform(AffiliateCommission $commission)
    {
        $totalMember = $this->data['total_member']->where('parent_id', $commission->user_id)->first();
        $newSignMember = $this->data['new_sign_count']->where('parent_id', $commission->user_id)
            ->where('month', substr($commission->start_at->toDateString(), 0,7))
            ->first();
        $totalMemberCount = !empty($totalMember) ? $totalMember->total_member : 0;
        $newSignMemberCount = !empty($newSignMember) ? $newSignMember->total_member : 0;

        return  [
            'Currency'              => $commission->currency,
            'Affiliate ID'          => $commission->user_name,
            'UAP'                   => $commission->active_count,
            'Total Member'          => $totalMemberCount,
            'New Sign Count'        => $newSignMemberCount,
            'Total Deposit'         => $commission->deposit,
            'Total Withdrawal'      => $commission->withdrawal,
            'Total Rake Amount'     => $commission->rake,
            'Adjustment'            => $commission->affiliate_adjustment,
            'Total Stake'           => $commission->stake,
            'Win/Loss'              => $commission->profit,
            'Rebate'                => $commission->rebate,
            'Promotion'             => $commission->promotion,
            'Transaction Cost'      => $commission->transaction_cost,
            'Net Loss'              => $commission->net_loss,
            'Commission Rate'       => !empty($commission->calculate_setting) ? $commission->calculate_setting['value'] : 0,
            'Previous Balance'      => $commission->previous_remain_commission,
            'Payout Comm'           => $commission->payout_commission,
            'Sub Aff Payout %'      => $commission->sub_commission_percent . '%',
            'Sub AFF Payout'        => $commission->sub_commission,
            'B/F'                   => $commission->previous_remain_commission,
        ];
    }

}