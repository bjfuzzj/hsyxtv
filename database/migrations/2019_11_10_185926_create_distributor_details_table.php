<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->default(0)->comment('用户ID');
            $table->unsignedBigInteger('order_id')->default(0)->comment('订单ID');
            $table->unsignedInteger('product_id')->default(0)->comment('产品ID');
            $table->unsignedInteger('buy_num')->default(0)->comment('用户购买数量');
            $table->decimal('rebate_percentage', 4, 2)->default(0)->comment('返利百分比');
            $table->decimal('rebate_amount')->default(0)->comment('返利金额');
            $table->decimal('single_rebate_amount')->default(0)->comment('单个产品返利金额');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态 1:待结算 2:可提现 3:失效');
            $table->string('invalid_reason')->default('')->comment('失效原因');
            $table->unsignedTinyInteger('source')->default(0)->comment('来源 1:来源自己推广 2:来源下级分销员推广');
            $table->unsignedInteger('source_user_id')->default(0)->comment('来源下级分销员用户ID');
            $table->timestamps();
            $table->index(['user_id'], 'idx_user_id');
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
        Schema::dropIfExists('distributor_details');
    }
}
