<?php

namespace App\Transformers;

use App\Models\GamePlatformTransferDetail;
use App\Models\UserAccount;

/**
 * @OA\Schema(
 *   schema="HistoryFundTransfer",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="交易id"),
 *   @OA\Property(property="order_no", type="string", description="订单号"),
 *   @OA\Property(property="from", type="string", description="来源"),
 *   @OA\Property(property="to", type="string", description="目的"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="显示状态"),
 *   @OA\Property(property="amount", type="string", description="金额"),
 *   @OA\Property(property="created_at", type="string", description="建立日期")
 * )
 */
class HistoryFundTransferTransformer extends Transformer
{
    public function transform($data)
    {
        switch ($data->status) {
            case GamePlatformTransferDetail::STATUS_SUCCESS:
                $status = 1;
                $displayStatus = __('history.successful');
                break;
            case GamePlatformTransferDetail::STATUS_FAIL:
                $status = 2;
                $displayStatus = __('history.failed');
                break;
            default:
                $displayStatus = __('history.pending');
                $status = 3;
                break;
        }

        $from = $data->from;
        if (UserAccount::isMainWallet($from)) {
            $from = UserAccount::getLangName();
        }else{
            $from = $data->platform->name;
        }

        $to = $data->to;
        if (UserAccount::isMainWallet($to)) {
            $to = UserAccount::getLangName();
        }else{
            $to = $data->platform->name;
        }

        return [
            'id'             => $data->id,
            'order_no'       => $data->order_no,
            'from'           => $from,
            'to'             => $to,
            'amount'         => thousands_number($data->amount),
            'status'         => $status,
            'display_status' => $displayStatus,
            'created_at'     => convert_time($data->created_at),
        ];
    }
}
