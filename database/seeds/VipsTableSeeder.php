<?php

use Illuminate\Database\Seeder;
use App\Models\Vip;

class VipsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        Vip::query()->truncate();

        $now = now();

        $vips = [
            [
                'id'            => 1,
                'level'         => 1,
                'name'          => 'NORMAL',
                'display_name'  => 'NORMAL',
                'rule'          => 0,
                'remark'        => '',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 2,
                'level'         => 2,
                'name'          => 'COUNT',
                'display_name'  => 'COUNT',
                'rule'          => 20000,
                'remark'        => '',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 3,
                'level'         => 3,
                'name'          => 'VISCOUNT',
                'display_name'  => 'VISCOUNT',
                'rule'          => 40000,
                'remark'        => '',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 4,
                'level'         => 4,
                'name'          => 'ARCHDUKE',
                'display_name'  => 'ARCHDUKE',
                'rule'          => 60000,
                'remark'        => '',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 5,
                'level'         => 5,
                'name'          => 'GRAND DUKE',
                'display_name'  => 'GRAND DUKE',
                'rule'          => 80000,
                'remark'        => '',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 6,
                'level'         => 6,
                'name'          => 'DUKE',
                'display_name'  => 'DUKE',
                'rule'          => 100000,
                'remark'        => '',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        Vip::query()->insert($vips);
        
    }
}