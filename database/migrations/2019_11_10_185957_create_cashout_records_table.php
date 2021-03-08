<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashoutRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashout_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->default(0)->comment('用户ID');
            $table->decimal('amount_total')->default(0.00)->comment('提现金额');
            $table->decimal('amount_payment')->default(0.00)->comment('真实支付金额');
            $table->decimal('person_taxes')->default(0.00)->comment('个人所得税');
            $table->string('open_id', 128)->default('')->comment('用户openid');
            $table->string('trade_no', 128)->default('')->comment('提现商户订单号');
            $table->string('payment_no', 128)->default('')->comment('微信付款单号');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态 1:待审核 2:提现成功 3:提现失败');
            $table->string('fail_reason')->default('')->comment('失败原因');
            $table->timestamp('payment_time')->nullable()->comment('微信支付交易时间');
            $table->timestamps();
            $table->index(['user_id'], 'idx_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cashout_records');
    }
}
