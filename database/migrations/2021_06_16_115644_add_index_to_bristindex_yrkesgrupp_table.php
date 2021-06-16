<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToBristindexYrkesgruppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bristindex_yrkesgrupp', function (Blueprint $table) {
            $table->index('yrkesgrupp_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bristindex_yrkesgrupp', function (Blueprint $table) {
            $table->dropIndex(['yrkesgrupp_id']);
        });
    }
}
