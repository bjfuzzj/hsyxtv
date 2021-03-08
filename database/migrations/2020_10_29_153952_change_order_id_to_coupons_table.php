<?php
/*
 * @Author: your name
 * @Date: 2020-10-29 15:39:52
 * @LastEditTime: 2020-10-29 15:40:42
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: /sg/songguo/database/migrations/2020_10_29_153952_change_order_id_to_coupons_table.php
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOrderIdToCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->bigInteger('order_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            //
        });
    }
}
