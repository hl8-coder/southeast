<?php

use Illuminate\Database\Seeder;

class BanksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('banks')->truncate();

        $banks = [
            [
                'name'          => 'Techcombank',
                'languages'     => '[{"front_name":"Techcombank", "language":"vi-VN"},{"front_name":"Techcombank", "language":"en-US"}]',
                'code'          => 'TCB',
                'currency'      => 'VND',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Sacombank',
                'languages'     => '[{"front_name":"Sacombank", "language":"vi-VN"},{"front_name":"Sacombank", "language":"en-US"}]',
                'code'          => 'SACOM',
                'currency'      => 'VND',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Vietcombank',
                'languages'     => '[{"front_name":"Vietcombank", "language":"vi-VN"},{"front_name":"Vietcombank", "language":"en-US"}]',
                'code'          => 'VCB',
                'currency'      => 'VND',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Asia Commercial Bank',
                'languages'     => '[{"front_name":"Asia Commercial Bank", "language":"vi-VN"},{"front_name":"Asia Commercial Bank", "language":"en-US"}]',
                'code'          => 'ACB',
                'currency'      => 'VND',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'BIDV',
                'languages'     => '[{"front_name":"BIDV", "language":"vi-VN"},{"front_name":"BIDV", "language":"en-US"}]',
                'code'          => 'BIDV',
                'currency'      => 'VND',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'DAB',
                'languages'     => '[{"front_name":"DAB", "language":"vi-VN"},{"front_name":"DAB", "language":"en-US"}]',
                'code'          => 'DAB',
                'currency'      => 'VND',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'EXB',
                'languages'     => '[{"front_name":"EXB", "language":"vi-VN"},{"front_name":"EXB", "language":"en-US"}]',
                'code'          => 'EXB',
                'currency'      => 'VND',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'VTB',
                'languages'     => '[{"front_name":"VTB", "language":"vi-VN"},{"front_name":"VTB", "language":"en-US"}]',
                'code'          => 'VTB',
                'currency'      => 'VND',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'AGRI',
                'languages'     => '[{"front_name":"AGRI", "language":"vi-VN"},{"front_name":"AGRI", "language":"en-US"}]',
                'code'          => 'AGRI',
                'currency'      => 'VND',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            # THB
            [
                'name'          => 'Kasikorn Bank',
                'languages'     => '[{"front_name":"ธนาคารกสิกรไทย", "language":"th"},{"front_name":"Kasikorn Bank", "language":"en-US"}]',
                'code'          => 'KB',
                'currency'      => 'THB',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Krungthai Bank',
                'languages'     => '[{"front_name":"ธนาคารกรุงไทย", "language":"th"},{"front_name":"Krungthai Bank", "language":"en-US"}]',
                'code'          => 'KTB',
                'currency'      => 'THB',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Siam Commercial Bank',
                'languages'     => '[{"front_name":"ธนาคารไทยพาณิชย์", "language":"th"},{"front_name":"Siam Commercial Bank", "language":"en-US"}]',
                'code'          => 'SCB',
                'currency'      => 'THB',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Bangkok Bank',
                'languages'     =>'[{"front_name":"ธนาคารกรุงเทพ", "language":"th"},{"front_name":"Bangkok Bank", "language":"en-US"}]',
                'code'          => 'BBL',
                'currency'      => 'THB',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Thai Military Bank',
                'languages'     =>'[{"front_name":"ธนาคารทหารไทย", "language":"th"},{"front_name":"Thai Military Bank", "language":"en-US"}]',
                'code'          => 'TMB',
                'currency'      => 'THB',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        \App\Models\Bank::insert($banks);
    }
}
