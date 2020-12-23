<?php

use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('languages')->delete();

        \DB::table('languages')->insert(array(
            0 =>
                array(
                    'id'         => 1,
                    'name'       => 'Chinese',
                    'code'       => 'zh-CN',
                    'created_at' => '2019-06-15 13:13:38',
                    'updated_at' => '2019-06-15 13:17:20',
                ),
            1 =>
                array(
                    'id'         => 2,
                    'name'       => 'English',
                    'code'       => 'en-US',
                    'created_at' => '2019-06-15 13:14:28',
                    'updated_at' => '2019-06-15 13:14:28',
                ),
            2 =>
                array(
                    'id'         => 3,
                    'name'       => 'Vietnamese',
                    'code'       => 'vi-VN',
                    'created_at' => '2019-06-15 13:14:14',
                    'updated_at' => '2019-06-15 13:14:14',
                ),
            3 =>
                array(
                    'id'         => 4,
                    'name'       => 'Thai',
                    'code'       => 'th',
                    'created_at' => '2019-06-15 13:14:28',
                    'updated_at' => '2019-06-15 13:14:28',
                ),
        ));


    }
}
