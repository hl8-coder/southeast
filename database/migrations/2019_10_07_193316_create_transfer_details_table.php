<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no')->default('')->comment('订单号');
            $table->unsignedInteger('user_id')->index();
            $table->string('user_name')->index();
            $table->unsignedInteger('to_user_id');
            $table->string('to_user_name');
            $table->decimal('from_before_balance', 16, 6)->default(0)->comment('来源转账前金额');
            $table->decimal('from_after_balance', 16, 6)->default(0)->comment('来源转账后金额');
            $table->decimal('amount', 16, 6)->default(0)->comment('转账金额');
            $table->unsignedTinyInteger('status')->default(\App\Models\TransferDetail::STATUS_CREATED);
            $table->string('remark', 1024)->default('');
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
        Schema::dropIfExists('transfer_details');
    }
}
