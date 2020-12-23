<?php
namespace App\Repositories;

use App\Models\GameBetDetail;
use Carbon\Carbon;
use DB;

class GameBetDetailRepository
{
    public static function profitInfo()
    {
        $select = "game_type,platform_code,sum(platform_profit) as platform_profit,sum(user_bet) as user_bet,count(1) as bet_count,count(distinct user_name) active_count";

        $ORM = GameBetDetail::selectRaw($select)
                ->where("payout_at", ">=", Carbon::now()->startOfMonth())
                ->where("payout_at", "<=", Carbon::now()->endOfMonth())
                ->groupBy(["game_type", "platform_code"]);

        return $ORM;
    }

    public static function profitInfoByName($parentId)
    {
        $ORM = self::profitInfo();

        $ORM->whereHas('user', function($query) use ($parentId) {
            $query->where('parent_id', $parentId);
        });

        return $ORM;
    }

    public static function subProfitInfoByName($parentId)
    {
        $ORM = self::profitInfo();

        $ORM->whereHas('user', function($query) use ($parentId) {
            $query->whereHas('parentUser', function($query) use ($parentId) {
                $query->where('parent_id', $parentId);
            });        
        });

        return $ORM;
    }

}