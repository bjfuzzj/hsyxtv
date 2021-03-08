<?php
/*
 * @Author: your name
 * @Date: 2021-01-17 21:06:24
 * @LastEditTime: 2021-01-17 21:07:14
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/database/migrations/2021_01_17_210624_add_pay_user_id_to_order_pay_record_table.php
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayUserIdToOrderPayRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_pay_records', function (Blueprint $table) {
            $table->unsignedInteger('pay_user_id')->default(0)->comment('支付用户ID')->after('order_id');
            $table->index(['pay_user_id'], 'idx_pay_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_pay_records', function (Blueprint $table) {
            $table->dropColumn('pay_user_id');
        });
    }
}
