<?php

use Illuminate\Database\Seeder;

class ExchangeRatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('exchange_rates')->delete();
        
        \DB::table('exchange_rates')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_currency'     => 'VND',
                'platform_currency' => 'USD',
                'conversion_value' => '0.0430',
                'inverse_conversion_value' => '23.2400',
            ),
        ));
    }
}