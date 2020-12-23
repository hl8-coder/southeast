<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index()->comment('会员id');
            $table->string('user_name')->index();
            $table->string('currency')->comment('币别');
            $table->string('order_no')->default('')->comment('订单号');
            $table->unsignedInteger('vip_id')->nullable();
            $table->string('user_ip')->default('')->comment('会员ip');
            $table->decimal('amount', 16, 6)->default(0)->comment('申请提现金额');
            $table->decimal('fee', 12, 6)->default(0)->comment('手续费');
            $table->decimal('remain_amount', 16, 6)->default(0)->comment('剩余未出款金额');
            $table->unsignedTinyInteger('device')->default(\App\Models\User::DEVICE_PC)->comment('设备类型');
            $table->unsignedSmallInteger('bank_id')->index()->comment('银行id');
            $table->string('province')->default('')->comment('省');
            $table->string('city')->default('')->comment('市');
            $table->string('branch')->default('')->comment('分行');
            $table->string('account_no')->comment('开户号码');
            $table->string('account_name')->comment('开户人姓名');
            $table->json('records')->nullable()->comment('出款信息');
            $table->unsignedTinyInteger('hold_reason')->nullable()->comment('hold理由');
            $table->unsignedTinyInteger('reject_reason')->nullable()->comment('拒绝理由');
            $table->unsignedTinyInteger('escalate_reason')->nullable()->comment('提升理由');
            $table->string('remark', 2048)->default('');
            $table->dateTime('paid_at')->nullable()->comment('支付时间');
            $table->json('verify_details')->nullable()->comment('审核明细');
            $table->unsignedTinyInteger('status')->default(\App\Models\Withdrawal::STATUS_PENDING);
            $table->unsignedTinyInteger('hold_status')->nullable()->comment('hold之前的状态');
            $table->dateTime('last_access_at')->nullable()->comment('最后访问管理人员时间');
            $table->string('last_access_name')->nullable()->comment('最后访问管理人员名称');
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
        Schema::dropIfExists('withdrawals');
    }
}
