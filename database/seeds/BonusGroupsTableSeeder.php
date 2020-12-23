<?php

use Illuminate\Database\Seeder;

class BonusGroupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('bonus_groups')->delete();
        
        \DB::table('bonus_groups')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'A',
                'admin_name' => 'left',
                'created_at' => '2019-06-20 20:34:44',
                'updated_at' => '2019-06-20 20:34:44',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'B',
                'admin_name' => 'left',
                'created_at' => '2019-06-20 20:34:53',
                'updated_at' => '2019-06-20 20:34:53',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'C',
                'admin_name' => 'left',
                'created_at' => '2019-06-20 20:34:57',
                'updated_at' => '2019-06-20 20:34:57',
            ),
        ));
        
        
    }
}