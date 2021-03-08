<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_incomes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->default(0)->comment('用户ID');
            $table->decimal('income_total')->default(0.00)->comment('累计收入');
            $table->decimal('income_used')->default(0.00)->comment('已提现的收入');
            $table->decimal('income_usable')->default(0.00)->comment('可提现的收入');
            $table->decimal('income_freeze')->default(0.00)->comment('待结算的收入');
            $table->decimal('person_taxes')->default(0.00)->comment('个人所得税');
            $table->timestamps();
            $table->unique(['user_id'], 'uniq_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_incomes');
    }
}
