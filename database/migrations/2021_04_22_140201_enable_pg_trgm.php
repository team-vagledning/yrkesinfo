<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnablePgTrgm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(DB::raw('CREATE EXTENSION IF NOT EXISTS pg_trgm'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement(DB::raw('DROP EXTENSION IF EXISTS pg_trgm'));
    }
}
