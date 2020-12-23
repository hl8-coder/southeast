<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliateSubTransferBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_sub_transfer_balances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('affiliate_id')->index()->comment('付款方代理ID'); //透過 affiliate id 找 user info
            $table->unsignedInteger('user_id')->index()->comment('付款方會員ID');
            $table->unsignedInteger('to_user_id')->index()->comment('收款方會員ID');
            $table->decimal('amount', 16, 6)->default(0)->comment('转帐金额');
            $table->decimal('balance_before', 16, 6)->default(0)->comment('转帐前馀额');
            $table->decimal('balance_after', 16, 6)->default(0)->comment('转帐后馀额');
            $table->string('currency')->default('')->comment('币别');
//            $table->unsignedTinyInteger('status')->default(1)->comment('转帐状态(1建立，2处理中，3处理成功，4处理失败，5已退款)');
            $table->string('transfer_ip')->default('')->comment('转出方IP');
            $table->dateTime('succeed_at')->nullable()->comment('转帐成功时间');
            $table->dateTime('refund_at')->nullable()->comment('退款成功时间');
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
        Schema::dropIfExists('affiliate_sub_transfer_balances');
    }
}
