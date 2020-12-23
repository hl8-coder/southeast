<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentPlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_platforms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('支付平台名称');
            $table->string('display_name')->comment('支付平台前台显示名称');
            $table->string('code')->comment('辨识码')->unique();
            $table->string('remarks')->default('')->comment('充值说明信息');

            $table->string('devices')->default('')->comment('可用装置');
            $table->string('currencies')->comment('可用币别');
            $table->unsignedTinyInteger('payment_type')->default(1)->comment('支付类型');
            $table->string('customer_id')->default('')->comment('商户id');
            $table->string('customer_key', 1024)->default('')->comment('商户私钥');
            $table->string('request_url')->default('')->comment('充值提交地址');

            $table->unsignedTinyInteger('request_type')->default(1)->comment('请求类型');
            $table->boolean('is_need_type_amount')->default(true)->comment('是否需要输入金额');
            $table->decimal('max_deposit', 16, 4)->default(0)->comment('单笔最大充值金额');
            $table->decimal('min_deposit', 16, 4)->default(0)->comment('单笔最低充值金额');
            $table->boolean('is_fee')->default(false)->comment('是否需要手续费');
            $table->decimal('fee_rebate', 5, 4)->default(0)->comment('充值手续费百分比');
            $table->decimal('min_fee', 12, 4)->default(0)->comment('最小手续费');
            $table->decimal('max_fee', 12, 4)->default(0)->comment('最大手续费');

            $table->string('image_path', 255)->default('')->comment('图片地址');

            $table->unsignedSmallInteger('sort')->default(0);
            $table->unsignedTinyInteger('status')->default(1);

            $table->softDeletes();
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
        Schema::dropIfExists('payment_platforms');
    }
}
