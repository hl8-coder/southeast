<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category')->comment('分类名称');
            $table->text('message')->nullable(false)->comment('内容');
            $table->integer('successNum')->default(0)->comment('发送成功数量');
            $table->integer('failureNum')->default(0)->comment('发送失败数量');
            $table->integer('totalNum')->default(0)->comment('发送总人数');
            $table->unsignedInteger('sent_admin_id')->comment('发送人id');
            $table->string('sent_admin_name')->comment('发送人');
            $table->index(['sent_admin_id','created_at'],'sent_infos');
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
        Schema::dropIfExists('notification_messages');
    }
}
