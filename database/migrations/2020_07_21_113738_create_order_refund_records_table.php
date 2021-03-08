<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderRefundRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_refund_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('order_id')->default(0)->comment('订单编号');
            $table->unsignedInteger('pay_record_id')->default(0)->comment('支付记录ID');
            $table->decimal('amount')->default(0.00)->comment('退款金额');
            $table->string('out_refund_no')->default('')->comment('订单退款流水号');
            $table->string('escrow_refund_no')->default('')->comment('第三方退款流水号');
            $table->unsignedTinyInteger('refund_status')->default(1)->comment('状态：1:申请退款 2:退款中 3:退款完成 4:退款失败');
            $table->timestamp('refund_time')->nullable()->comment('退款时间');
            $table->timestamps();
            $table->index(['order_id'], 'idx_order');
            $table->index(['pay_record_id'], 'idx_pay_record_id');
            $table->index(['out_refund_no'], 'idx_out_refund_no');
            $table->index(['escrow_refund_no'], 'idx_escrow_refund_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_refund_records');
    }
}
