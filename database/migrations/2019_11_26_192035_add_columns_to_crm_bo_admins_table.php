<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToCrmBoAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_bo_admins', function (Blueprint $table) {
            $table->boolean('on_duty')->default(false)->comment('上班状态')->after('status');
            $table->integer('tag_admin_id')->nullable()->comment('排版管理员ID');
            $table->string('tag_admin_name')->nullable()->comment('排版管理员名称');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            Schema::table('crm_bo_admins', function (Blueprint $table) {
                $table->dropColumn('on_duty');
            });
        } catch (Exception $exception) {

        }
        try {
            Schema::table('crm_bo_admins', function (Blueprint $table) {
                $table->dropColumn('tag_admin_id');
            });
        } catch (Exception $exception) {

        }
        try {
            Schema::table('crm_bo_admins', function (Blueprint $table) {
                $table->dropColumn('tag_admin_name');
            });
        } catch (Exception $exception) {

        }

    }
}
