<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Modules\Yrkesstatistik\Collection;
use App\Modules\Yrkesstatistik\Entry;
use App\Region;
use App\Yrkesgrupp;
use App\Yrkesomrade;
use App\YrkesstatistikAggregated;
use Arr;
use GuzzleHttp\Client;
use Str;

class YrkesgruppAggregator extends BaseAggregator
{
    const YEAR = '2017';

    public function run()
    {
        $YEAR = self::YEAR;

        $yrkesgrupper = Yrkesgrupp::get();

        foreach ($yrkesgrupper as $yrkesgrupp) {
            /*
            $regioner = resolve(Region::class)->get()->map(function ($region) use ($yrkesgrupp) {
                return [
                    'id' => $region->external_id,
                    'namn' => $region->name,
                    'anstallda' => 0,
                    'ledigaJobb' => $this->getAntalAnstalldaIRegion($yrkesomrade->external_id, $region->external_id),
                    'bristindex' => $this->getBristindexForRegion($yrkesomrade, $region->id)
                ];
            })->toArray();*/

            $aggregated = $yrkesgrupp->yrkesstatistikAggregated()->orderBy('created_at', 'desc')->first();

            $collection = (new Collection())->initializeFromArray($aggregated->statistics);

            $anstallda = $collection->findFirstByKeysAndKeyValues(
                ["Anställda", "Län", "Kön", "År"],
                [ScbFormatter::$regioner['00'], ScbFormatter::$kon['1+2'], $YEAR]
            )->getValue();


            $sektorer = collect($collection->findAllByKeysAndKeyValues(
                ["Anställda", "Sektor", "Kön", "År"],
                [["Offentlig", "Privat"], ScbFormatter::$kon['1+2'], $YEAR]
            ))->map(function (Entry $entry) {
                return [
                    'name' => $entry->getKeyValue('Sektor'),
                    'anstallda' => $entry->getValue(),
                ];
            });


                // Karta
                /*
                foreach ($regioner as $key => $values) {
                    $region = $values['namn'];

                    $anstalldaRegion = $collection->findFirstByKeysAndKeyValues(
                        ["Anställda", "Län", "Kön", "År"],
                        [$region, ScbFormatter::$kon['1+2'], $YEAR]
                    )->getValue();

                    $regioner[$key]['anstallda'] = $values['anstallda'] + $anstalldaRegion;
                }
                */

            $r = [
                "anstallda" => $anstallda,
                "sektorer" => $sektorer,
                //"bristindex" => $this->getBristindex($yrkesomrade),
                //"regioner" => $regioner,
            ];

            $yrkesgrupp->update([
                'aggregated_statistics' => $r
            ]);

        }
    }


    public function getBristindex($yrkesomrade)
    {
        return self::round($yrkesomrade->bristindex()->ettAr()->avg('bristindex'));
    }

    public function getBristindexForRegion($yrkesomrade, $regionId)
    {
        return self::round($yrkesomrade->bristindex()->ettAr()->where('region_id', $regionId)->avg('bristindex'));
    }

    public static function round($value)
    {
        return number_format(round($value, 2), 2, '.', '');
    }
}
