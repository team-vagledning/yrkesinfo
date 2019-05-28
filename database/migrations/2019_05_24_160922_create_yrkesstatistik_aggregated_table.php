<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYrkesstatistikAggregatedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yrkesstatistik_aggregated', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('yrkesgrupp_id')->unsigned();
            $table->jsonb('statistics');
            $table->timestamps();

            $table->foreign('yrkesgrupp_id')->references('id')->on('yrkesgrupper');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yrkesstatistik_aggregated');
    }
}
