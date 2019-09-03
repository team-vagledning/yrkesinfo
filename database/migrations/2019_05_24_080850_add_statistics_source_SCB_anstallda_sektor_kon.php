<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatisticsSourceSCBAnstalldaSektorKon extends Migration
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
            'name' => 'anstallda,sektor,kon',
            'description' => 'Anställda (yrkesregistret) 16-64 år efter Yrke (SSYK 2012), arbetsställets sektortillhörighet, kön och år',
            'meta' => json_decode($data),
            'aggregator' => \App\Aggregators\Yrkesstatistik\AnstalldaSektorKon::class,
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
                "code": "ArbetsSektor",
                "selection": {
                    "filter": "item",
                    "values": ["11", "1110", "1120", "1130", "15", "1510", "1520", "1530", "1540", "1560", "US"]
                }
            }, {
                "code": "Kon",
                "selection": {
                    "filter": "item",
                    "values": ["1", "2"]
                }
            }],
            "endpoint": "http://api.scb.se/OV0104/v1/doris/sv/ssd/START/AM/AM0208/AM0208E/YREG50",
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
