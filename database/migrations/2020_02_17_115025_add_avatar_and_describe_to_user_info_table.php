<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAvatarAndDescribeToUserInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_info', function (Blueprint $table) {
            $table->string('avatar')->after('birth_at')->nullable()->comment('代理头像');
            $table->text('describe')->after('avatar')->nullable()->comment('代理自述');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_info', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'describe']);
        });
    }
}
