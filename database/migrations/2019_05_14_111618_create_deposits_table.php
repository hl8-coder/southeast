<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index(); 
            $table->string('user_ip')->default('')->comment('发起充值ip');
            $table->string('currency')->default('')->comment('币别');
            $table->string('language')->default('')->comment('语言');
            $table->string('order_no')->default('')->comment('交易订单号');

            $table->unsignedSmallInteger('payment_type')->comment('支付类型, 1=Online Banking(银行卡), 2=Quickpay(第三方充值), 3=Mpay(app充值), 4=Scratch Card(点数卡)');
            $table->unsignedSmallInteger('payment_platform_id')->index()->comment('支付平台id');

            # amount相关
            $table->decimal('amount', 16, 4)->default(0)->comment('申请充值金额');
            $table->decimal('receive_amount', 16, 4)->default(0)->comment('实际收款金额');
            $table->decimal('arrival_amount', 16, 4)->default(0)->comment('实际上分金额(信用上分金额)');
            $table->decimal('bank_fee', 16, 4)->default(0)->comment('手续费');
            $table->decimal('reimbursement_fee', 16, 4)->default(0)->comment('报销费(公司承担手续用)');
            $table->unsignedTinyInteger('is_partial')->default(0)->comment('是否部份上分');
            $table->decimal('partial_amount', 16, 4)->default(0)->comment('部份上分金額');
            $table->unsignedInteger('partial_remark_id')->nullable()->index();

            # 公司银行卡相关
            $table->unsignedSmallInteger('company_bank_account_id')->nullable()->index()->comment('公司银行卡id');
            $table->string('company_bank_code')->nullable()->comment('公司卡银行辨识码');
            $table->string('company_bank_branch')->nullable()->comment('公司卡银行分行');
            $table->string('company_bank_account_name')->nullable()->comment('公司卡开户人姓名');
            $table->string('company_bank_account_no')->nullable()->comment('公司卡开户账号');

            $table->string('online_banking_channel')->nullable()->comment('公司卡支付渠道, 1=ATM, 2=Internet Banking, 3=Mobile Banking, 4=Over the Counter, 5=Cash Deposit');
            $table->unsignedSmallInteger('user_bank_account_id')->nullable()->index()->comment('会员银行卡id');
            $table->string('user_bank_id')->nullable()->comment('会员银行id');
            $table->string('user_bank_account_name')->nullable()->comment('会员开户人姓名');
            $table->string('user_bank_account_no')->nullable()->comment('会员开户账号');
            $table->string('receipts')->default('')->comment('凭证图片id(,逗号分割)');
            $table->unsignedTinyInteger('receipt_count')->default(0)->comment('凭证个数');
            $table->string('reference_id')->nullable()->comment('银行回应码');
            $table->string('deposit_date')->nullable()->comment('支付日期');

            # Thirdparty
            $table->string('payment_platform_order_no')->nullable()->comment('支付平台订单号');
            $table->string('payment_reference')->nullable()->comment('支付平台回应码');
            $table->string('payment_bank_code')->nullable()->comment('支付平台银行');

            # Mpay
            $table->string('user_mpay_number')->nullable()->comment('会员Mpay帐号');
            $table->string('mpay_trading_code')->nullable()->comment('Mpay追踪码');

            # Scratch Card
            $table->string('card_type')->nullable()->comment('点数卡类型');
            $table->string('pin_number')->nullable()->comment('点数卡PIN NUMBER');
            $table->string('serial_number')->nullable()->comment('点数卡SERIAL NUMBER');

            $table->unsignedSmallInteger('statement_id')->nullable()->index()->comment('银行对应ID');
            $table->dateTime('statement_at')->nullable()->comment('银行对应时间');
            $table->string('fund_in_account')->default('')->comment('实际收款帐号');
            
            $table->unsignedSmallInteger('hold_reason')->default(0)->comment('保留原因');
            $table->unsignedSmallInteger('reject_reason')->default(0)->comment('拒绝原因');
            $table->unsignedTinyInteger('need_second_approve')->default(0)->comment('需要二次批准');
            $table->unsignedTinyInteger('is_advance_credit')->default(0)->comment('是否進入上分流程');

            $table->string('remarks')->default('')->comment('备注');
            $table->dateTime('deposit_at')->nullable()->comment('申请支付时间');

            $table->dateTime('start_processed_at')->nullable()->comment('开始处理时间, 对应到 Deposit Date');
            $table->dateTime('approved_at')->nullable()->comment('上分时间(到账时间), 对应到 Transfer Date');

            $table->string('button_flow_code')->default('1')->comment('功能按钮流程代码');
            
            $table->unsignedTinyInteger('tag_category')->nullable()->comment('标签类型');
            $table->string('tag_remarks')->nullable()->comment('标签备注');
            $table->unsignedTinyInteger('tag')->default(\App\Models\Deposit::TAG_OPEN)->comment('标签');
            $table->unsignedTinyInteger('status')->default(\App\Models\Deposit::STATUS_CREATED);

            $table->string('callback_content', 2048)->default('')->comment('第三方回调内容');            
            $table->dateTime('callback_at')->nullable()->comment('回调时间');
            $table->string('sys_remarks')->default('')->comment('系统备注');            

            # 流水要求
            $table->boolean('is_turnover_closed')->default(false)->comment('流水限制是否关闭');
            $table->decimal('turnover_closed_value', 16, 6)->default(0)->comment('所需流水总数');
            $table->decimal('turnover_current_value', 16, 6)->default(0)->comment('当前流水数值');
            $table->dateTime('turnover_closed_at')->nullable();
            $table->string('turnover_closed_admin_name')->nullable();

            $table->timestamps(); //created_at 对应到 layout Transaction Date
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposits');
    }
}
