<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('trace_id')->nullable()->comment('追踪id')->index();
            $table->string('order_no')->default('')->comment('订单号');

            # 帐变类型 start
            $table->unsignedTinyInteger('type_group')->comment('帐变大分类');
            $table->unsignedTinyInteger('type')->comment('帐变分类')->index();
            # 帐变类型 end

            # 金额 start
            $table->string('currency')->default('')->comment('币别');
            $table->boolean('is_income')->default(false)->comment('是否入账 true:入账 false:出账');
            $table->decimal('amount', 16, 6)->default(0)->comment('帐变金额');
            $table->decimal('before_balance', 16, 6)->default(0)->comment('帐变前总金额');
            $table->decimal('after_balance', 16, 6)->default(0)->comment('帐变后总金额');
            # 金额 end

            # 备注 start
            $table->string('admin_name')->nullable()->comment('管理员名称');
            $table->string('admin_remark', 2048)->default('')->comment('管理员备注');
            $table->string('sys_remark', 2048)->default('')->comment('系统备注');
            # 备注 end

            # 处理时间 start
            $table->dateTime('start_process_at')->nullable()->comment('处理开始时间');
            $table->dateTime('end_process_at')->nullable()->comment('处理完成时间');
            # 处理时间 end

            $table->unsignedTinyInteger('status')->default(\App\Models\Transaction::STATUS_CREATED);

            $table->unique(['user_id', 'type', 'trace_id'], 'user_type_trace');

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
        Schema::dropIfExists('transactions');
    }
}
