<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToTaxonomyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yrkesomraden', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('yrkesgrupper', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('yrkesbenamningar', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yrkesomraden', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('yrkesgrupper', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('yrkesbenamningar', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
