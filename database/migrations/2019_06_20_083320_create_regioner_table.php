<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegionerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regioner', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('external_id')->nullable();
            $table->string('name');
            $table->timestamps();
        });

        $this->data()->each(function ($name, $externalId) {
            \App\Region::create([
                'external_id' => $externalId,
                'name' => $name
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('regioner');
    }

    public function data()
    {
        return collect([
            "10" => "Blekinge län",
            "20" => "Dalarnas län",
            "9" => "Gotlands län",
            "21" => "Gävleborgs län",
            "13" => "Hallands län",
            "23" => "Jämtlands län",
            "6" => "Jönköpings län",
            "8" => "Kalmar län",
            "7" => "Kronobergs län",
            "25" => "Norrbottens län",
            "12" => "Skåne län",
            "1" => "Stockholms län",
            "4" => "Södermanlands län",
            "3" => "Uppsala län",
            "17" => "Värmlands län",
            "24" => "Västerbottens län",
            "22" => "Västernorrlands län",
            "19" => "Västmanlands län",
            "14" => "Västra Götalands län",
            "18" => "Örebro län",
            "5" => "Östergötlands län",
        ]);
    }
}
