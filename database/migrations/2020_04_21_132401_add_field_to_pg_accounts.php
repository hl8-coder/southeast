<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToPgAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pg_accounts', function (Blueprint $table) {
            $table->string('username')->default('')->comment('登录账号');
            $table->string('password')->default('')->comment('登录密码');
            $table->string('email')->default('')->comment('邮箱');
            $table->string('email_password')->default('')->comment('邮箱密码');
            $table->unsignedTinyInteger('otp')->nullable()->comment('关联密码');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pg_accounts', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropColumn('password');
            $table->dropColumn('email');
            $table->dropColumn('email_password');
            $table->dropColumn('otp');
        });
    }
}
