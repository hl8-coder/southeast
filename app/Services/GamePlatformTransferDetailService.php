<?php

namespace App\Services;

use App\Models\GamePlatformTransferDetail;

class GamePlatformTransferDetailService
{
    public function fail(GamePlatformTransferDetail $detail, $remark)
    {
        if ($detail->fail($remark) && $detail->userBonusPrize) {
            $detail->userBonusPrize->fail();
        }
    }

    public function success(GamePlatformTransferDetail $detail)
    {

    }
}