<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdateByToCrmResources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_resources', function (Blueprint $table) {
            $table->string('upload_by')->nullable()->comment('上传人员');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_resources', function (Blueprint $table) {
            $table->dropColumn('upload_by');
        });
    }
}
