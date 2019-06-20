<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBristindexYrkesgruppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bristindex_yrkesgrupp', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('yrkesgrupp_id')->unsigned();
            $table->foreign('yrkesgrupp_id')->references('id')->on('yrkesgrupper');

            $table->bigInteger('region_id')->unsigned();
            $table->foreign('region_id')->references('id')->on('regioner');

            $table->decimal('bristindex');

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
        Schema::dropIfExists('bristindex_yrkesgrupp');
    }
}
