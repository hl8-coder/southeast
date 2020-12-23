<?php

use App\Models\Bonus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_claim')->default(false)->comment('是否需要申请');
            $table->unsignedTinyInteger('category')->comment('新旧红利');
            $table->json('languages')->comment('标题多语言内容');
            $table->string('code')->comment('代码')->unique();
            $table->string('platform_code')->comment('平台代码')->index();
            $table->string('product_code')->comment('产品代码')->index();
            $table->dateTime('effective_start_at')->nullable()->comment('开始时间');
            $table->dateTime('effective_end_at')->nullable()->comment('结束时间');
            $table->dateTime('sign_start_at')->nullable()->comment('申请开始时间');
            $table->dateTime('sign_end_at')->nullable()->comment('申请结束时间');
            $table->boolean('status')->default(false);

            $table->unsignedInteger('bonus_group_id')->comment('红利分组id')->index();
            $table->string('bonus_group_name')->comment('红利分组名称')->index();
            $table->unsignedTinyInteger('type')->comment('计算类型(固定值/百分比)');
            $table->unsignedInteger('rollover')->default(0)->comment('流水倍数(本金+红利)');
            $table->decimal('amount', 14, 4)->default(0)->comment('计算数值');
            $table->boolean('is_auto_hold_withdrawal')->default(true)->comment('是否添加remark');
            $table->unsignedTinyInteger('cycle')->default(Bonus::CYCLE_WHOLE)->comment('周期');
            $table->unsignedTinyInteger('user_type')->default(Bonus::USER_TYPE_RISK_AND_PAYMENT)->comment('会员类型');
            $table->json('risk_group_ids')->nullable()->comment('风控组别');
            $table->json('payment_group_ids')->nullable()->comment('支付组别');
            $table->json('user_ids')->nullable()->comment('红利参与会员id列表');

            $table->json('currencies')->comment('币别');
            $table->string('admin_name');
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
        Schema::dropIfExists('bonuses');
    }
}
