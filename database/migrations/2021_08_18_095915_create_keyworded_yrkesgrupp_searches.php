<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeywordedYrkesgruppSearches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keyworded_yrkesgrupp_searches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('keyword');

            $table->bigInteger('yrkesgrupp_id')->unsigned();
            $table->foreign('yrkesgrupp_id')->references('id')->on('yrkesgrupper');

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
        Schema::dropIfExists('keyworded_yrkesgrupp_searches');
    }
}
