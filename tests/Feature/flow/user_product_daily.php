<?php

namespace Tests\Feature\flow;

use App\Models\UserProductDailyReport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class user_product_daily extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreate()
    {
        # 获取会员
        $users = \App\Models\User::where([
            ['id', '>', 403],
            ['is_agent', false]
        ])
            ->pluck('id');
        # 获取投注记录
        $reports = UserProductDailyReport::all();

        foreach ($reports as $report) {
            $year              = mt_rand(2018, 2019);
            $mon               = mt_rand(1, 12);
            $mon               = $mon > 9 ? $mon : '0' . $mon;
            $day               = mt_rand(1, 28);
            $day               = $day > 9 ? $day : '0' . $day;
            $id                = mt_rand(0, 99);
            $user              = \App\Models\User::find($users[$id]);
            $report->user_id   = $user->id;
            $report->user_name = $user->name;
            $report->date      = $year . '-' . $mon . '-' . $day;
            $report->save();
        }
    }
}
