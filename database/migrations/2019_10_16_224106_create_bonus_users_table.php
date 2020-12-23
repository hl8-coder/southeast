<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonusUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('bonus_id');
            $table->unsignedInteger('user_id');
            $table->string('child_bonus_code')->nullable()->comment('子红利code')->unique();
            $table->timestamps();
            $table->index(['bonus_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonus_users');
    }
}
