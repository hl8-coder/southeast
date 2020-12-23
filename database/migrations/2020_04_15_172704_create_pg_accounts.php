<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePgAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pg_accounts', function (Blueprint $table) {
            $table->increments('id')->comment('pg account 金额变动表');
            $table->string('payment_platform_code')->index()->comment('关联的支付方式code');
            $table->decimal('current_balance', 16, 6)->default(0)->comment('当前余额');
            $table->boolean('status')->default(true)->comment('状态');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pg_accounts');
    }
}
