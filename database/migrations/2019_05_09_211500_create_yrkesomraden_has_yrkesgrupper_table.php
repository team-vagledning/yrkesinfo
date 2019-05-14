<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYrkesomradenHasYrkesgrupperTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yrkesomraden_has_yrkesgrupper', function (Blueprint $table) {
            $table->bigInteger('yrkesomrade_id')->unsigned();
            $table->foreign('yrkesomrade_id')->references('id')->on('yrkesomraden');

            $table->bigInteger('yrkesgrupp_id')->unsigned();
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
        Schema::dropIfExists('yrkesomraden_has_yrkesgrupper');
    }
}
