<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Affiliate;

class CreateAffiliatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('code')->default('')->comment('代理号');
            $table->string('refer_by_code')->default('')->comment('上级代理号');
            $table->boolean('is_fund_open')->default(false)->comment('是否开启转帐功能');
            $table->json('commission_setting')->nullable()->comment('代理奖励设定, JSON 格式存储');
            $table->unsignedTinyInteger('cs_status')->default(Affiliate::CS_STATUS_PENDING)->comment('代理奖励设定状态');
            $table->unsignedTinyInteger('cs_cycles')->default(Affiliate::CS_CYCLE_ONE_MONTH)->comment('代理奖励设定状态, 预设一个月');
            $table->dateTime('cs_status_last_updated_at')->nullable()->comment('最后更新代理奖励设定状态时间');
            $table->string('cs_last_updated_name')->nullable()->comment('最后更新代理奖励设定状态管理员名称');
            $table->boolean('is_become_user')->default(false)->comment('是否已执行代理转会员, 是的话则代理资讯不可用');
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
        Schema::dropIfExists('affiliates');
    }
}
