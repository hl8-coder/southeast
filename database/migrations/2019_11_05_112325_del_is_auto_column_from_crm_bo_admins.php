<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DelIsAutoColumnFromCrmBoAdmins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_bo_admins', function (Blueprint $table) {
            $table->dropColumn('is_auto_for_welcome');
            $table->dropColumn('is_auto_for_retention');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_bo_admins', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_auto_for_welcome')->default(0)->comment('是否为自动分派 WELCOME');
            $table->unsignedTinyInteger('is_auto_for_retention')->default(0)->comment('是否为自动分派 Retention');
        });
    }
}
