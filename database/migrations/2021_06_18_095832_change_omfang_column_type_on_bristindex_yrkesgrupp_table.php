<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeOmfangColumnTypeOnBristindexYrkesgruppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE bristindex_yrkesgrupp ALTER COLUMN omfang TYPE INTEGER USING omfang::integer");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE bristindex_yrkesgrupp ALTER COLUMN omfang TYPE VARCHAR");
    }
}
