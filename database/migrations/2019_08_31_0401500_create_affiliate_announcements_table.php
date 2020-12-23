<?php

use App\Models\AffiliateAnnouncement;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_announcements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('currencies')->default('');
            $table->string('name')->default('')->comment('标题');
            $table->json('content')->comment('内容');
            $table->unsignedTinyInteger('category')->default(AffiliateAnnouncement::CATEGORY_BANKING_OPTION)->comment('分类');
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
        Schema::dropIfExists('affiliate_announcements');
    }
}
