<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuantityToOrderLogisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_logistics', function (Blueprint $table) {
            $table->integer('quantity')->unsigned()->default(0)->comment('发货数量')->after('receiver_address');
            $table->softDeletes();
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
            $table->dropColumn(['quantity', 'deleted_at']);
        });
    }
}
