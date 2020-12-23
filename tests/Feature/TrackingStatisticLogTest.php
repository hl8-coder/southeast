<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackingStatisticLogTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        # 生成数据
        $trackingStatistics = [];
        for ($i = 1; $i < 101; $i++) {
            $trackingStatistics[] = [
                'tracking_name' => 'tracking_name' . $i,
                'user_id'       => $i,
                'user_name'     => 'user_name' . $i,
                'date'          => now(),
            ];
        }
        DB::table('tracking_statistics')->truncate();
        DB::table('tracking_statistics')->insert($trackingStatistics);
        $this->creativeResource();
    }

    public function creativeResource()
    {
        $creativeResources = [];
        for ($i = 1; $i < 1001; $i++) {
            $tracking            = mt_rand(1, 100);
            $creativeResources[] = [
                'type'          => 1,
                'group'         => mt_rand(1, 2),
                'size'          => mt_rand(1, 177),
                'tracking_id'   => $tracking,
                'tracking_name' => 'tracking_name' . $tracking,
                'currency'      => 'VND',
                'banner_path'   => 'uploads/images/201910/03/10_1570074311_dmksstQU7X.jpeg',
                'banner_url'    => 'https://www.baidu.com/',
            ];
        }
        DB::table('creative_resources')->truncate();
        DB::table('creative_resources')->insert($creativeResources);
        DB::table('tracking_statistic_logs')->truncate();
        factory(\App\Models\TrackingStatisticLog::class, 10000)->create();
    }
}
