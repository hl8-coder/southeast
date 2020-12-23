<?php

use Illuminate\Database\Seeder;
use App\Models\ChangingConfig;

class ChangingConfigsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        ChangingConfig::query()->truncate();

        $configs = [
            [
                'code'          => 'ibc_last_version_key',
                'name'          => 'IBC报表的last_version_key',
                'remark'        => 'IBC报表的last_version_key',
                'is_front_show' => false,
                'type'          => 'string',
                'value'         => '0',
            ],
        ];

        ChangingConfig::insert($configs);

    }
}