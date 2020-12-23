<?php

use Illuminate\Database\Seeder;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Country::query()->truncate();
        $data = [
            [
                'country'      => 'China',
                'country_code' => 'CHN',
                'currency'     => 'CNY',
                'remark'       => '中国, China',
            ],
            [
                'country'      => 'Vietnam',
                'country_code' => 'VNM',
                'currency'     => 'VND',
                'remark'       => '越南, Vietnam',
            ],
            [
                'country'      => 'America',
                'country_code' => 'USA',
                'currency'     => 'USD',
                'remark'       => '美国, America',
            ],
            [
                'country'      => 'Thailand',
                'country_code' => 'THA',
                'currency'     => 'THB',
                'remark'       => '泰国, Thailand',
            ]
        ];
        \App\Models\Country::query()->insert($data);
    }
}
