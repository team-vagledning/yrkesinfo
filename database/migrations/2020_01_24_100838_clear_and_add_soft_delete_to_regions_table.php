<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ClearAndAddSoftDeleteToRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(DB::raw('DELETE FROM bristindex_yrkesgrupp'));
        DB::statement(DB::raw('DELETE FROM regioner'));

        Schema::table('regioner', function (Blueprint $table) {
            $table->softDeletes();
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
        Schema::table('regioner', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        $this->data()->each(function ($name, $externalId) {
            \App\Region::create([
                'external_id' => $externalId,
                'name' => $name
            ]);
        });
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
