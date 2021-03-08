<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsDefaultToOrderLogisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_logistics', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_default')->default(0)->comment('是否是默认地址')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_logistics', function (Blueprint $table) {
            $table->dropColumn(['is_default']);
        });
    }
}
