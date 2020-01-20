<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYrkesgrupperHasYrkesbenamningarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yrkesgrupper_has_yrkesbenamningar', function (Blueprint $table) {
            $table->bigInteger('yrkesgrupp_id')->unsigned();
            $table->foreign('yrkesgrupp_id')->references('id')->on('yrkesgrupper');

            $table->bigInteger('yrkesbenamning_id')->unsigned();
            $table->foreign('yrkesbenamning_id')->references('id')->on('yrkesbenamningar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yrkesgrupper_has_yrkesbenamningar');
    }
}
