<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionClaimUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_claim_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('promotion_id')->index();
            $table->string('promotion_code')->nullable()->index();
            $table->unsignedInteger('user_id')->index();
            $table->string('user_name')->index();
            $table->unsignedTinyInteger('related_type')->nullable()->comment('关联类型');
            $table->unsignedInteger('related_id')->nullable()->comment('关联id');
            $table->string('related_code')->nullable()->comment('关联code');
            $table->string('admin_name')->nullable();
            $table->unsignedTinyInteger('status')->default(\App\Models\PromotionClaimUser::STATUS_CREATED);
            $table->string('front_remark', 1024)->default('')->comment('前台备注');
            $table->string('remark')->default('')->comment('备注');
            $table->timestamps();
            $table->unique(['user_id', 'promotion_id'], 'unique_key');
            $table->index(['user_id', 'related_type', 'related_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_claim_users');
    }
}
