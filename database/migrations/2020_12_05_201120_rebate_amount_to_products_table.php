<?php
/*
 * @Author: your name
 * @Date: 2020-12-05 20:11:20
 * @LastEditTime: 2020-12-05 20:12:33
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/database/migrations/2020_12_05_201120_rebate_amount_to_products_table.php
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RebateAmountToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('first_rebate_amount')->default(0)->comment('一级分销返佣金额')->after('share_poster');
            $table->decimal('second_rebate_amount')->default(0)->comment('二级分销返佣金额')->after('first_rebate_amount');
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
            $table->dropColumn(['first_rebate_amount', 'second_rebate_amount']);
        });
    }
}
