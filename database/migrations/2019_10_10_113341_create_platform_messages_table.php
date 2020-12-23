<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('platform_code');
            $table->unsignedTinyInteger('type')->comment('保存数据类型');
            $table->string('key')->comment('key');
            $table->string('value')->default('')->comment('值');
            $table->timestamps();
            $table->index(['platform_code', 'type', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('platform_messages');
    }
}
