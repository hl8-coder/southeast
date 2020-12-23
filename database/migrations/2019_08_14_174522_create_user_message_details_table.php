<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMessageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_message_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_message_id')->comment('会员消息的ID');
            $table->integer('receive_user_id')->comment('接收人的ID');
            $table->string('receive_user_name')->comment('接收人');
            $table->string('phone', 30)->nullable(false)->comment('电话号码');
            $table->string('receive_user_status')->default('')->comment('接收人接收时的状态');
            $table->string('currency')->default('')->comment('币别');
            $table->tinyInteger('status')->default(1)->comment('短信状态');
            $table->text('desc')->nullable(true)->comment('备注');
            $table->index(['receive_user_id','status','created_at'],'sent_details');
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
        Schema::dropIfExists('user_message_details');
    }
}
