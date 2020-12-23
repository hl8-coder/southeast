<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliateCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->string('user_name');
            $table->string('currency')->index();
            $table->unsignedInteger('affiliate_id')->index(); //透過 affiliate id 找 user info

            # 计算时的代理设置
            $table->json('calculate_setting')->nullable()->comment('代理奖励计算设置');
            # 计算时的代理设置

            # 代理银行卡 start
            $table->unsignedSmallInteger('bank_id')->nullable()->default(null)->comment('银行id');
            $table->string('province')->nullable()->comment('省');
            $table->string('city')->nullable()->comment('市');
            $table->string('branch')->nullable()->comment('分行');
            $table->string('account_no')->nullable()->comment('开户号码');
            $table->string('account_name')->nullable()->comment('开户人姓名');
            # 代理银行卡 end

            # 计算基础数据 start
            $table->decimal('profit', 16, 6)->default(0)->comment('团队盈亏(相对于代理)');
            $table->decimal('stake', 16, 6)->default(0)->comment('总会员赌注金额');
            $table->decimal('deposit', 16, 6)->default(0)->comment('总会员存款金额');
            $table->decimal('withdrawal', 16, 6)->default(0)->comment('总会员提领金额');
            $table->decimal('rebate', 16, 6)->default(0)->comment('总会员游戏返水金额');
            $table->decimal('promotion', 16, 6)->default(0)->comment('总会员活动金额');
            $table->decimal('rake', 16, 6)->default(0)->comment('抽水金额, API 棋牌类抽水');
            $table->decimal('sub_adjustment', 16, 6)->default(0)->comment('下级调整金额');
            $table->decimal('affiliate_adjustment', 16, 6)->default(0)->comment('代理调整金额');
            $table->unsignedInteger('active_count')->default(0)->comment('当月活跃玩家数量');
            # 计算基础数据 end

            # 计算数据 start
            $table->decimal('transaction_cost', 16, 6)->default(0)->comment('交易手续费');
            $table->decimal('bear_cost', 16, 6)->default(0)->comment('代理承担费用');
            $table->decimal('net_loss', 16, 6)->default(0)->comment('代理调整金额');
            $table->decimal('product_cost', 16, 6)->default(0)->comment('产品费用');
            # 计算数据 end

            # 反给上级代理佣金 start
            $table->decimal('parent_commission', 16, 6)->default(0)->comment('上级代理佣金');
            # 反给上级代理佣金 end

            # 下级直属代理 start
            $table->decimal('sub_commission', 16, 6)->default(0)->comment('下级代理佣金');
            $table->decimal('sub_commission_percent', 5, 2)->default(0)->comment('下级代理佣金百分比');
            # 下级直属代理 end

            # 周期剩余分红 start
            $table->decimal('previous_remain_commission', 16, 6)->default(0)->comment('上周期未付款的代理分红');
            $table->decimal('remain_commission', 16, 6)->default(0)->comment('本周期未付款代理分红');
            # 周期剩余分红 end

            # 总分红 start
            $table->decimal('total_commission', 16, 6)->default(0)->comment('总分红金额');
            $table->decimal('payout_commission', 16, 6)->default(0)->comment('真实出款代理奖励金额');
            # 总分红 end

            $table->date('start_at')->nullable()->comment('周期计算开始时间');
            $table->date('end_at')->nullable()->comment('周期计算结束时间');
            $table->unsignedTinyInteger('status')->default(\App\Models\AffiliateCommission::STATUS_PENDING)->comment('状态');

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
        Schema::dropIfExists('affiliate_commissions');
    }
}
