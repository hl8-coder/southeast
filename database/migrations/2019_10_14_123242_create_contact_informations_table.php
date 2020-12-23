<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->json('currencies')->comment('币别');
            $table->json('languages')->comment('多语言内容');
            $table->boolean('is_affiliate')->default(false)->comment('代理展示');
            $table->boolean('is_enable')->default(true)->comment('是否启用');
            $table->string('icon')->default('')->comment('图标');
            $table->string('api_url')->default('')->comment('第三方api');
            $table->integer('sort')->default(0)->comment('排序');
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
        Schema::dropIfExists('contact_informations');
    }
}
