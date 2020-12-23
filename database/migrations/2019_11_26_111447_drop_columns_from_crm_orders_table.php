<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnsFromCrmOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_orders', function (Blueprint $table) {
            $table->dropColumn('channel');
            $table->dropColumn('purpose');
            $table->dropColumn('prefer_product');
            $table->dropColumn('source');
            $table->dropColumn('prefer_bank');
            $table->dropColumn('bank_remark');
            $table->dropColumn('reason');
            $table->dropColumn('reason_remark');
            $table->dropColumn('remark');
            $table->dropColumn('last_edit_admin_id');
            $table->dropColumn('last_edit_admin_name');
            $table->dropColumn('amount');
            $table->dropColumn('call_status');
            $table->dropColumn('is_auto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('channel')->default(0)->comment('联络渠道');
            $table->unsignedTinyInteger('purpose')->default(0)->comment('联络目的');
            $table->unsignedTinyInteger('prefer_product')->default(0)
                ->comment('产品活动优惠');
            $table->unsignedTinyInteger('source')->default(0)->comment('客户来源');
            $table->unsignedTinyInteger('prefer_bank')->default(0)
                ->comment('偏好银行');
            $table->string('bank_remark', 1024)->default('')->comment('银行备注');
            $table->unsignedTinyInteger('reason')->default(0)->comment('原因');
            $table->string('reason_remark', 1024)->default('')->comment('原因备注');
            $table->string('remark', 1024)->default('')->comment('备注');
            $table->unsignedInteger('last_edit_admin_id')->nullable()->comment('最后编辑管理员id');
            $table->string('last_edit_admin_name')->nullable()->comment('最后编辑管理员');
            $table->decimal('amount', 16, 6)->default(0)->comment('优惠金额');
            $table->unsignedTinyInteger('call_status')->default(0)->comment('联络状态');
            $table->boolean('is_auto')->default(false)->comment('是否为系统自动指派');
        });
    }
}
