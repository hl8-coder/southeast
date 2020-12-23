<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjustments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no')->default('')->comment('订单号');
            $table->unsignedInteger('transaction_id')->nullable();
            $table->unsignedInteger('user_id')->index();
            $table->string('user_name')->index();
            $table->unsignedTinyInteger('type')->comment('类型');
            $table->unsignedTinyInteger('category')->comment('分类');
            $table->string('product_code')->default('')->comment('product备注');
            $table->decimal('amount', 16, 6)->default(0)->comment('金额');
            $table->unsignedTinyInteger('status')->default(\App\Models\Adjustment::STATUS_PENDING);
            $table->string('created_admin_name')->nullable();
            $table->string('verified_admin_name')->nullable();
            $table->dateTime('verified_at')->nullable()->comment('审核时间');
            $table->string('remark', 2048)->default('')->comment('备注');
            $table->string('reason', 2048)->default('')->comment('理由');

            # 流水要求
            $table->boolean('is_turnover_closed')->default(false)->comment('流水限制是否关闭');
            $table->decimal('turnover_closed_value', 16, 6)->default(0)->comment('所需流水总数');
            $table->decimal('turnover_current_value', 16, 6)->default(0)->comment('当前流水数值');
            $table->dateTime('turnover_closed_at')->nullable();
            $table->string('turnover_closed_admin_name')->nullable();
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
        Schema::dropIfExists('adjustments');
    }
}
