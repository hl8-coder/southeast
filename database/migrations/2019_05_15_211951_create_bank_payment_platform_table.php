<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankPaymentPlatformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_payment_platform', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('payment_platform_id')->index();
            $table->unsignedSmallInteger('bank_id')->index();
            $table->string('bank_code')->default('')->comment('实际商户关联银行辨识码');
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
        Schema::dropIfExists('bank_payment_platform');
    }
}
