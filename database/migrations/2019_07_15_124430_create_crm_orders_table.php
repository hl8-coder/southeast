<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmOrdersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('crm_orders', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('会员id')->index();
            $table->unsignedInteger('affiliate_id')->nullable()->index();
            $table->decimal('amount', 16, 6)->default(0)->comment('优惠金额');
            $table->unsignedTinyInteger('type')
                ->default(\App\Models\CrmOrder::TYPE_WELCOME)->comment('记录类型');
            $table->unsignedTinyInteger('call_status')->default(0)->comment('联络状态');
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
            $table->unsignedInteger('tag_admin_id')->nullable()->comment('标记管理员id');
            $table->string('tag_admin_name')->nullable()->comment('标记管理员');
            $table->timestamp('tag_at')->nullable()->comment('标记详细时间');
            $table->unsignedInteger('last_edit_admin_id')->nullable()->comment('最后编辑管理员id');
            $table->string('last_edit_admin_name')->nullable()->comment('最后编辑管理员');
            $table->unsignedInteger('admin_id')->nullable()->comment('管理员(BO user)id, 被指派');
            $table->string('admin_name')->nullable()->comment('管理员(BO user), 被指派');
            $table->unsignedTinyInteger('is_auto')->default(0)->comment('是否为系统指派');
            $table->unsignedInteger('last_save_case_admin_id')->nullable()->comment('最后联系客户案例管理员id');
            $table->string('last_save_case_admin_name')->nullable()->comment('最后联系客户案例管理员');
            $table->timestamp('last_save_case_at')->nullable()->comment('最后联系客户案例详细时间');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        Schema::dropIfExists('crm_orders');
    }
}
