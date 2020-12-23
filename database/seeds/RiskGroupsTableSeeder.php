<?php

use Illuminate\Database\Seeder;
use App\Models\RiskGroup;

class RiskGroupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        RiskGroup::query()->truncate();

        $now = now();

        $riskGroups = [
            [
                'id'            => 1,
                'name'          => 'A',
                'description'   => 'A',
                'sort'          => 0,
                'status'        => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 2,
                'name'          => 'VNAFF',
                'description'   => 'VNAFF',
                'sort'          => 0,
                'status'        => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 3,
                'name'          => 'THBAFF',
                'description'   => 'THBAFF',
                'sort'          => 0,
                'status'        => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 4,
                'name'          => 'N',
                'description'   => 'N',
                'sort'          => 0,
                'status'        => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 5,
                'name'          => 'N1',
                'description'   => 'N1',
                'sort'          => 0,
                'status'        => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 6,
                'name'          => 'N2',
                'description'   => 'N2',
                'sort'          => 0,
                'status'        => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 7,
                'name'          => 'N3',
                'description'   => 'N3',
                'sort'          => 0,
                'status'        => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 8,
                'name'          => 'V1',
                'description'   => 'V1',
                'sort'          => 0,
                'status'        => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 9,
                'name'          => 'V2',
                'description'   => 'V2',
                'sort'          => 0,
                'status'        => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 10,
                'name'          => 'V3',
                'description'   => 'V3',
                'sort'          => 0,
                'status'        => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 11,
                'name'          => 'V4',
                'description'   => 'V4',
                'sort'          => 0,
                'status'        => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 12,
                'name'          => 'V5',
                'description'   => 'V5',
                'sort'          => 0,
                'status'        => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        RiskGroup::query()->insert($riskGroups);
    }
}