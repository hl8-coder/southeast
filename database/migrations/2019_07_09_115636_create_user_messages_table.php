<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('category')->comment('分类名称');
            $table->text('content')->nullable(false)->comment('内容');
            $table->integer('number')->default(0)->comment('发送总人数');
            $table->unsignedInteger('sent_admin_id')->comment('发送人id');
            $table->string('sent_admin_name')->comment('发送人');
            $table->string('provider_code')->default('')->comment('供应商编码');
            $table->tinyInteger('use_type')->comment('使用类型');
            $table->string('title')->default('')->comment('短信标题');
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
        Schema::dropIfExists('user_messages');
    }
}
