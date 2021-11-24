<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBristindexGroupingsHasYrkesgrupperTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bristindex_groupings_has_yrkesgrupper', function (Blueprint $table) {
            $table->bigInteger('bristindex_grouping_id')->unsigned();
            $table->foreign('bristindex_grouping_id')->references('id')->on('bristindex_groupings');

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
        Schema::dropIfExists('bristindex_groupings_has_yrkesgrupper');
    }
}
