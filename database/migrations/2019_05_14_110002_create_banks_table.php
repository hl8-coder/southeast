<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('银行名称');
            $table->json('languages')->comment('银行前端显示名称多语言设置');
            $table->string('code')->unique()->comment('银行辨识码');
            $table->string('currency')->comment('币别')->index();
            $table->decimal('min_balance', 16, 6)->default(0)->comment('最低金额');
            $table->decimal('daily_limit', 16, 6)->default(0)->comment('日限制');
            $table->decimal('annual_limit', 16, 6)->default(0)->comment('总流水限制(充值+提款)');
            $table->unsignedTinyInteger('status')->default(true);
            $table->string('admin_name')->default('');
            $table->string('image')->default('')->comment('图片');
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
        Schema::dropIfExists('banks');
    }
}
