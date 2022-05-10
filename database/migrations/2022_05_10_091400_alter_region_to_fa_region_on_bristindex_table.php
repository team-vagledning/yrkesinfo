<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRegionToFaRegionOnBristindexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bristindex', function (Blueprint $table) {
            $table->bigInteger('fa_region_id')->unsigned()->nullable();
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
        Schema::table('bristindex', function (Blueprint $table) {
            $table->dropForeign(['fa_region_id']);
            $table->dropColumn('fa_region_id');
        });
    }
}
