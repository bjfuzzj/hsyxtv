<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorPercentagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_percentages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('retailkey')->default('')->comment('分销比例Key');
            $table->integer('percentage')->unsigned()->default(100)->comment('分销返利百分比');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_percentages');
    }
}
