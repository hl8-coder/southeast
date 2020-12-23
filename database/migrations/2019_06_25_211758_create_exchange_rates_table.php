<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangeRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_currency')->comment('会员币别');
            $table->string('platform_currency')->comment('第三方币别');
            $table->decimal('conversion_value', 8, 4)->default(0)->comment('正向汇率');
            $table->decimal('inverse_conversion_value', 8, 4)->default(0)->comment('逆向汇率');
            $table->unique(['user_currency', 'platform_currency'], 'currency_user_platform_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exchange_rates');
    }
}
