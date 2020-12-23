<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bank_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedSmallInteger('bank_id')->index();
            $table->boolean('is_preferred')->default(false)->comment('是否首选');
            $table->string('province')->default('')->comment('省');
            $table->string('city')->default('')->comment('市');
            $table->string('branch')->default('')->comment('分行');
            $table->string('account_name', 50)->comment('户名');
            $table->string('account_no')->default('')->comment('开户账号');
            $table->unsignedTinyInteger('status')->default(true);
            $table->dateTime('last_used_at')->nullable()->comment('最后使用时间');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_bank_accounts');
    }
}
