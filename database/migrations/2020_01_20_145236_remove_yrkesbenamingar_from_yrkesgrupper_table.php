<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveYrkesbenamingarFromYrkesgrupperTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yrkesgrupper', function (Blueprint $table) {
            $table->dropColumn('yrkesbenamningar');
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
            $table->jsonb('yrkesbenamningar')->nullable();
        });
    }
}
