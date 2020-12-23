<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToCrmOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_orders', function (Blueprint $table) {
            $table->boolean('is_auto')->default(false)->comment('是否为系统自动指派');
            $table->string('affiliated_code')->nullable()->comment('affiliates code');
            $table->boolean('status')->default(false)->comment('crm order status');
            $table->boolean('call_status')->nullable()->comment('订单联络状态');
            $table->unsignedTinyInteger('batch')->default(1)->comment('数据批次');
        });

        DB::statement('alter table `crm_orders` add index user_id_type_status(`user_id`, `type`, `status`)');
        DB::statement('alter table `crm_orders` add index type_status(`type`, `status`)');
        DB::statement('alter table `crm_orders` add index user_id_type(`user_id`, `type`)');
        DB::statement('alter table `crm_orders` add index user_id(`user_id`)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try{
            Schema::table('crm_orders', function (Blueprint $table) {
                $table->dropColumn('batch');
                $table->dropColumn('is_auto');
            });
        }catch (Exception $e){

        }
        try{
            Schema::table('crm_orders', function (Blueprint $table) {
                $table->dropColumn('affiliated_code');
            });
        }catch (Exception $e){

        }
        try{
            Schema::table('crm_orders', function (Blueprint $table) {
                $table->dropColumn('affiliate_code');
            });
        }catch (Exception $e){

        }
        try{
            Schema::table('crm_orders', function (Blueprint $table) {
                $table->dropColumn('status');
                $table->dropColumn('call_status');
                $table->dropIndex('user_id');
                $table->dropIndex('user_id_type');
                $table->dropIndex('type_status');
                $table->dropUnique('user_id_type_status');
            });
        }catch (Exception $e){

        }
    }
}
