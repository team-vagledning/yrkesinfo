<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveBristindexFromYrkesgrupper extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yrkesgrupper', function (Blueprint $table) {
            $table->dropColumn('bristindex');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yrkesgrupper', function (Blueprint $table) {
            $table->decimal('bristindex')->nullable();
        });
    }
}
