<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatisticsSourceSCBLonSektorKon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $data = self::data();

        \App\YrkesstatistikSource::create([
            'supplier' => 'SCB',
            'name' => 'lon,sektor,kon',
            'description' => 'Genomsnittlig månadslön och lönspridning efter sektor, Yrke (SSYK 2012), kön, tabellinnehåll och år',
            'meta' => json_decode($data),
            'aggregator' => \App\Aggregators\Yrkesstatistik\LonSektorKon::class,
        ]);
    }

    public static function data()
    {
        return <<<JSON
        {
            "query": [{
                "code": "Sektor",
                "selection": {
                    "filter": "item",
                    "values": ["0"]
                }
            }, {
                "code": "Yrke2012",
                "selection": {
                    "filter": "item",
                    "values": []
                }
            }, {
                "code": "Kon",
                "selection": {
                    "filter": "item",
                    "values": ["1", "2", "1+2"]
                }
            }, {
                "code": "ContentsCode",
                "selection": {
                    "filter": "item",
                    "values": ["000000C5", "000000C7", "000000CA"]
                }
            }],
            "endpoint": "http://api.scb.se/OV0104/v1/doris/sv/ssd/START/AM/AM0110/AM0110A/LoneSpridSektorYrk4A",
            "response": {
                "format": "json"
            }
        }
JSON;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\YrkesstatistikSource::orderBy('id', 'desc')->first()->delete();
    }
}
