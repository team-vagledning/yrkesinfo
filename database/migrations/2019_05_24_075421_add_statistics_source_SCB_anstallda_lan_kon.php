<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatisticsSourceSCBAnstalldaLanKon extends Migration
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
            'name' => 'anstallda,lan,kon',
            'description' => 'Anställda 16-64 år med arbetsplats i' .
                             'regionen (dagbef) efter län, yrke (4-siffrig SSYK 2012) och kön. År 2014 - 2017',
            'meta' => json_decode($data),
            'aggregator' => \App\Aggregators\Yrkesstatistik\AnstalldaLanKon::class,
        ]);
    }

    public static function data()
    {
        return <<<JSON
        {
            "query": [{
                "code": "Region",
                "selection": {
                    "filter": "vs:RegionLän99US",
                    "values": [
                        "01",
                        "03",
                        "04",
                        "05",
                        "06",
                        "07",
                        "08",
                        "09",
                        "10",
                        "12",
                        "13",
                        "14",
                        "17",
                        "18",
                        "19",
                        "20",
                        "21",
                        "22",
                        "23",
                        "24",
                        "25",
                        "99"
                    ]
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
                    "values": ["1", "2"]
                }
            }],
            "endpoint": "http://api.scb.se/OV0104/v1/doris/sv/ssd/START/AM/AM0208/AM0208M/YREG60",
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
