<?php

use Illuminate\Database\Seeder;

class RewardsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('rewards')->delete();
        
        \DB::table('rewards')->insert(array (
            0 => 
            array (
                'id' => 1,
                'level' => 1,
                'rule' => 2000,
                'remark' => '',
                'created_at' => '2019-06-14 16:25:09',
                'updated_at' => '2019-06-14 16:25:09',
            ),
            1 => 
            array (
                'id' => 2,
                'level' => 2,
                'rule' => 4000,
                'remark' => '',
                'created_at' => '2019-06-14 16:25:16',
                'updated_at' => '2019-06-14 16:25:16',
            ),
            2 => 
            array (
                'id' => 3,
                'level' => 3,
                'rule' => 6000,
                'remark' => '',
                'created_at' => '2019-06-14 16:25:22',
                'updated_at' => '2019-06-14 16:25:22',
            ),
            3 => 
            array (
                'id' => 4,
                'level' => 4,
                'rule' => 8000,
                'remark' => '',
                'created_at' => '2019-06-14 16:25:35',
                'updated_at' => '2019-06-14 16:25:35',
            ),
            4 => 
            array (
                'id' => 5,
                'level' => 5,
                'rule' => 10000,
                'remark' => '',
                'created_at' => '2019-06-14 16:25:42',
                'updated_at' => '2019-06-14 16:25:42',
            ),
            5 => 
            array (
                'id' => 6,
                'level' => 6,
                'rule' => 20000,
                'remark' => '',
                'created_at' => '2019-06-14 16:25:53',
                'updated_at' => '2019-06-14 16:25:53',
            ),
            6 => 
            array (
                'id' => 7,
                'level' => 7,
                'rule' => 40000,
                'remark' => '',
                'created_at' => '2019-06-14 16:26:00',
                'updated_at' => '2019-06-14 16:26:00',
            ),
            7 => 
            array (
                'id' => 8,
                'level' => 8,
                'rule' => 80000,
                'remark' => '',
                'created_at' => '2019-06-14 16:26:10',
                'updated_at' => '2019-06-14 16:26:10',
            ),
        ));
        
        
    }
}