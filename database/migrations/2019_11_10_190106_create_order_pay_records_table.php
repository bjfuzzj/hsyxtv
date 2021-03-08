<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPayRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_pay_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('order_id')->default(0)->comment('订单编号');
            $table->decimal('amount', 10, 2)->default(0.00)->comment('支付金额');
            $table->string('out_trade_no')->default('')->comment('订单支付流水号');
            $table->string('transaction_id')->default('')->comment('微信交易流水号');
            $table->string('wx_prepay_id')->default('')->comment('微信prepay_id');
            $table->unsignedTinyInteger('trade_status')->default(1)->comment('支付状态：1:未支付 2:支付中 3:已支付 4:支付失败');
            $table->timestamp('trade_time')->nullable()->comment('交易时间');
            $table->text('result')->nullable()->comment('支付结果详情');
            $table->timestamps();
            $table->index(['order_id'], 'idx_order');
            $table->index(['out_trade_no'], 'idx_out_trade_no');
            $table->index(['transaction_id'], 'idx_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_pay_records');
    }
}
