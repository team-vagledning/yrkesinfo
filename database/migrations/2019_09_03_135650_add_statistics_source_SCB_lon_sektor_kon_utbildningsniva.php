<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatisticsSourceSCBLonSektorKonUtbildningsniva extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
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
            'name' => 'lon,sektor,kon,utbildningsniva',
            'description' => 'Genomsnittlig grund- och månadslön samt kvinnors lön i procent av ' .
                             'mäns lön efter sektor, yrke (SSYK 2012), kön och utbildningsnivå (SUN)',
            'meta' => json_decode($data),
            'aggregator' => \App\Aggregators\Yrkesstatistik\LonSektorKonUtbildningsniva::class,
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
                        "values": [
                            "0",
                            "1-3",
                            "1",
                            "2",
                            "3",
                            "4-5",
                            "4",
                            "5"
                        ]
                    }
                },
                {
                    "code": "Yrke2012",
                    "selection": {
                        "filter": "item",
                        "values": []
                    }
                },
                {
                    "code": "Kon",
                    "selection": {
                        "filter": "item",
                        "values": [
                            "1",
                            "2",
                            "1+2"
                        ]
                    }
                },
                {
                    "code": "UtbildningsNiva",
                    "selection": {
                        "filter": "item",
                        "values": [
                            "TOTALT",
                            "1",
                            "2",
                            "3",
                            "4",
                            "5",
                            "6",
                            "7",
                            "US"
                        ]
                    }
                },
                {
                    "code": "ContentsCode",
                    "selection": {
                        "filter": "item",
                        "values": [
                            "000000C0"
                        ]
                    }
                }
            ],
            "response": {
                "format": "json"
            },
            "endpoint": "http://api.scb.se/OV0104/v1/doris/sv/ssd/START/AM/AM0110/AM0110A/LonYrkeUtbildning4A"
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
