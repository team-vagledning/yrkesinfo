<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYrkesgrupperTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yrkesgrupper', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ssyk');
            $table->jsonb('alternative_ssyk')->nullable();
            $table->string('name')->index();
            $table->text('description');
            $table->jsonb('yrkesbenamningar');
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
        Schema::dropIfExists('yrkesgrupper');
    }
}
