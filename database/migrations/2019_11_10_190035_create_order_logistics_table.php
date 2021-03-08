<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderLogisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_logistics', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('order_id')->unsigned()->default(0)->comment('订单ID');
            $table->string('receiver_name')->default('')->comment('收货人姓名');
            $table->string('receiver_mobile')->default('')->comment('收货人电话');
            $table->string('receiver_province', 32)->default('')->comment('收货人省份');
            $table->string('receiver_city', 64)->default('')->comment('收货人城市');
            $table->string('receiver_district', 64)->default('')->comment('收货人地区');
            $table->string('receiver_address')->default('')->comment('收货人详细地址');
            $table->string('express_no')->default('')->comment('快递单号');
            $table->string('express_name')->default('')->comment('快递公司');
            $table->boolean('status')->default(0)->comment('状态 1:待发货 2:已发货 3:确认收货');
            $table->timestamps();
            $table->index(['order_id'], 'idx_order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_logistics');
    }
}
