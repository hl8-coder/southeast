<?php

use App\Models\Announcement;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('')->comment('标题');
            $table->boolean('is_agent')->default(false)->comment('是否为代理公告');
            $table->json('content')->comment('内容');
            $table->json('currencies')->comment('币别');
            $table->unsignedTinyInteger('show_type')->default(Announcement::SHOW_TYPE_ALL)->comment('公告会员类型');
            $table->json('payment_group_ids')->nullable()->comment('支付组别id数组');
            $table->json('vip_ids')->nullable()->comment('vip组别id数组');
            $table->unsignedTinyInteger('category')->default(Announcement::CATEGORY_PAYMENT)->comment('分类');
            $table->dateTime('start_at')->nullable()->comment('开始时间');
            $table->dateTime('end_at')->nullable()->comment('结束时间');
            $table->string('admin_name')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('announcements');
    }
}
