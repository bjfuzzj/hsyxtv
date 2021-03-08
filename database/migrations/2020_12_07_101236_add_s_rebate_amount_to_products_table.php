<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSRebateAmountToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('s_first_rebate_amount')->default(0)->comment('银牌分销员一级分销返佣金额')->after('second_rebate_amount');
            $table->decimal('s_second_rebate_amount')->default(0)->comment('银牌分销员二级分销返佣金额')->after('s_first_rebate_amount');
            $table->decimal('t_first_rebate_amount')->default(0)->comment('金牌分销员一级分销返佣金额')->after('s_second_rebate_amount');
            $table->decimal('t_second_rebate_amount')->default(0)->comment('金牌分销员二级分销返佣金额')->after('t_first_rebate_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['s_first_rebate_amount', 's_second_rebate_amount','t_first_rebate_amount','t_second_rebate_amount']);
        });
    }
}
