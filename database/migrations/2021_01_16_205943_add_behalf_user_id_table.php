<?php
/*
 * @Author: your name
 * @Date: 2021-01-16 20:59:43
 * @LastEditTime: 2021-01-16 21:00:30
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/database/migrations/2021_01_16_205943_add_behalf_user_id_table.php
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBehalfUserIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('behalf_pay_user_id')->default(0)->comment('代付用户ID')->after('is_test');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('behalf_pay_user_id');
        });
    }
}
