<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->unique();
            $table->decimal('total_balance', 16, 6)->default(0)->comment('总金额');
            $table->decimal('freeze_balance', 16, 6)->default(0)->comment('冻结金额');
            $table->decimal('total_point_balance', 16, 6)->default(0)->comment('总积分');
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
        Schema::dropIfExists('user_accounts');
    }
}
