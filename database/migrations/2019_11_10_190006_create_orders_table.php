<?php
/*
 * @Author: your name
 * @Date: 2020-12-02 18:18:59
 * @LastEditTime: 2021-01-25 23:40:23
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/database/migrations/2019_11_10_190006_create_orders_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->comment('订单ID');
            $table->integer('user_id')->unsigned()->default(0)->comment('用户ID');
            $table->integer('product_id')->unsigned()->default(0)->comment('产品ID');
            $table->tinyInteger('product_type')->unsigned()->default(1)->comment('产品类型, 1:预售 2:正常 3:拼团');
            $table->integer('buy_num')->unsigned()->default(0)->comment('购买数量');
            $table->integer('status')->unsigned()->default(10)->comment('订单状态 (10:未付款, 20:已付款, 30:已发货, 40:确认收货, 80:已退款, 100:已取消, 110:过期)');
            $table->decimal('amount_total', 10, 2)->default(0.00)->comment('总价格（含优惠价格）');
            $table->decimal('amount_payable', 10, 2)->default(0.00)->comment('应付金额（不含优惠价格）');
            $table->decimal('amount_paid', 10, 2)->default(0.00)->comment('已付金额');
            $table->decimal('amount_coupon', 10, 2)->default(0.00)->comment('优惠金额');
            $table->decimal('amount_refundable', 10, 2)->default(0.00)->comment('应退金额');
            $table->decimal('amount_refunded', 10, 2)->default(0.00)->comment('已退金额');
            $table->timestamp('paid_time')->nullable()->comment('支付时间');
            $table->timestamp('expire_time')->nullable()->comment('过期时间');
            $table->timestamp('auto_finish_time')->nullable()->comment('自动确认收货时间');
            $table->timestamp('received_time')->nullable()->comment('确认收货时间');
            $table->tinyInteger('invalid_cause')->default(0)->comment('订单失效原因');
            $table->tinyInteger('is_test')->default(0)->comment('是否测试单 0:否, 1:是');
            $table->timestamps();
            $table->index(['user_id'], 'idx_user_id');
            $table->index(['product_id'], 'idx_product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
