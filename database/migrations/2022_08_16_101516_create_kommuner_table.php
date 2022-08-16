<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKommunerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kommuner', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('external_id');
            $table->bigInteger('region_id')->unsigned();
            $table->bigInteger('fa_region_id')->unsigned();
            $table->string('name');
            $table->timestamps();

            $table->foreign('region_id')->references('id')->on('regioner');
            $table->foreign('fa_region_id')->references('id')->on('fa_regioner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kommuner');
    }
}
