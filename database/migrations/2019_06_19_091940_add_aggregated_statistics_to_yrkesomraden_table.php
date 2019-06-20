<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAggregatedStatisticsToYrkesomradenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yrkesomraden', function (Blueprint $table) {
            $table->jsonb('aggregated_statistics')->nullable();
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
            $table->dropColumn('aggregated_statistics');
        });
    }
}
