<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('order_id')->unsigned()->default(0)->comment('订单ID');
            $table->boolean('operator_type')->default(0)->comment('操作人员类型 1:用户 2:系统 3:管理员');
            $table->integer('operator_id')->unsigned()->default(0)->comment('操作人员ID');
            $table->string('operator_name', 128)->default('')->comment('操作人员姓名');
            $table->string('desc')->comment('备注');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
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
        Schema::dropIfExists('order_logs');
    }
}
