<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeIndicies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yrkesomraden_has_yrkesgrupper', function (Blueprint $table) {
            $table->index('yrkesomrade_id');
            $table->index('yrkesgrupp_id');
        });

        Schema::table('bristindex_yrkesgrupp', function (Blueprint $table) {
            $table->index(['yrkesgrupp_id', 'omfang', 'artal']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yrkesomraden_has_yrkesgrupper', function (Blueprint $table) {
            $table->dropIndex(['yrkesomrade_id']);
            $table->dropIndex(['yrkesgrupp_id']);
        });

        Schema::table('bristindex_yrkesgrupp', function (Blueprint $table) {
            $table->dropIndex(['yrkesgrupp_id', 'omfang', 'artal']);
        });
    }
}
