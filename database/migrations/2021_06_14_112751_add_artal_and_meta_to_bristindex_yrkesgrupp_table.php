<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArtalAndMetaToBristindexYrkesgruppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bristindex_yrkesgrupp', function (Blueprint $table) {
            $table->year('artal')->nullable();
            $table->jsonb('meta')->nullable();
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
            $table->dropColumn('artal');
            $table->dropColumn('meta');
        });
    }
}
