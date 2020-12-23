<?php

use Illuminate\Database\Seeder;
use App\Models\PaymentGroup;

class PaymentGroupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        PaymentGroup::query()->truncate();

        $now = now();

        $paymentGroups = [
            [
                'id'                    => 1,
                'name'                  => 'A/N',
                'remark'                => 'A/N',
                'preset_risk_group_id'  => 1,
                'status'                => true,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'id'                    => 2,
                'name'                  => 'VIP1',
                'remark'                => 'VIP1',
                'preset_risk_group_id'  => 1,
                'status'                => true,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'id'                    => 3,
                'name'                  => 'VIP2',
                'remark'                => 'VIP2',
                'preset_risk_group_id'  => 1,
                'status'                => true,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'id'                    => 4,
                'name'                  => 'VIP3',
                'remark'                => 'VIP3',
                'preset_risk_group_id'  => 1,
                'status'                => true,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
        ];

        PaymentGroup::query()->insert($paymentGroups);
    }
}