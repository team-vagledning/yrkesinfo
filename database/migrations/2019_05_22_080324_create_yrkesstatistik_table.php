<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYrkesstatistikTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yrkesstatistik', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('yrkesstatistik_source_id')->unsigned();
            $table->bigInteger('yrkesgrupp_id')->unsigned();
            $table->jsonb('statistics');
            $table->timestamps();

            $table->foreign('yrkesstatistik_source_id')->references('id')->on('yrkesstatistik_sources');
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
        Schema::dropIfExists('yrkesstatistik');
    }
}
