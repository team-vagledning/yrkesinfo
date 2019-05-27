<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYrkesstatistikSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yrkesstatistik_sources', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('supplier');
            $table->string('name');
            $table->text('description');
            $table->jsonb('meta');
            $table->string('aggregator')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yrkesstatistik_sources');
    }
}
