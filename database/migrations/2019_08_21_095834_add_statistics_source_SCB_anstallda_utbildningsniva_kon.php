<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatisticsSourceSCBAnstalldaUtbildningsnivaKon extends Migration
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
            'name' => 'anstallda,utbildningsniva,kon',
            'description' => 'Anställda (yrkesregistret) 16-64 år efter ' .
                             'Yrke (SSYK 2012), Utbildningsnivå SUN 2000, ålder, kön och år',
            'meta' => json_decode($data),
            'aggregator' => \App\Aggregators\Yrkesstatistik\AnstalldaUtbildningsnivaKon::class,
        ]);
    }

    public static function data()
    {
        return <<<JSON
        {
            "query": [{
                "code": "Yrke2012",
                "selection": {
                    "filter": "item",
                    "values": []
                }
            }, {
                "code": "UtbNivaSUN2000",
                "selection": {
                    "filter": "item",
                    "values": ["1", "2", "3", "4", "5", "6", "7"]
                }
            }, {
                "code": "Alder",
                "selection": {
                    "filter": "item",
                    "values": ["16-24", "25-29", "30-34", "35-39", "40-44", "45-49", "50-54", "55-59", "60-64"]
                }
            }, {
                "code": "Kon",
                "selection": {
                    "filter": "item",
                    "values": ["1", "2"]
                }
            }],
            "endpoint": "http://api.scb.se/OV0104/v1/doris/sv/ssd/START/AM/AM0208/AM0208E/YREG51",
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
