<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Importers\JobSearch\Api\ApiImporter;
use App\Modules\Yrkesstatistik\Collection;
use App\Modules\Yrkesstatistik\Entry;
use App\Region;
use App\Yrkesgrupp;

class YrkesgruppAggregator extends BaseAggregator
{
    const YEAR = '2021';

    private $jobSearchApi;

    public function __construct(ApiImporter $jobSearchApi)
    {
        $this->jobSearchApi = $jobSearchApi;
    }

    public function run()
    {
        $YEAR = self::YEAR;

        $yrkesgrupper = Yrkesgrupp::get();

        foreach ($yrkesgrupper as $yrkesgrupp) {
            $regioner = resolve(Region::class)->get()->map(function ($region) use ($yrkesgrupp) {

                $this->jobSearchApi->addAsyncSearch('yrkesgrupp', $yrkesgrupp->external_id, $region->external_id);

                return [
                    'id' => $region->external_id,
                    'namn' => $region->name,
                    'anstallda' => 0,
                    'ledigaJobb' => 0,
                    'bristindex' => $this->getBristindexForRegion($yrkesgrupp, $region->id)
                ];
            })->toArray();

            $this->jobSearchApi->unwrap();

            foreach ($regioner as $key => $values) {
                $regioner[$key]['ledigaJobb'] = $this->jobSearchApi->getAsyncCount($yrkesgrupp->external_id, $values['id']);
            }

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

            // Löner
            $lon = [
                'medel' => 0,
                'percentil10' => 0,
                'percentil90' => 0,
            ];


            $lon['medel'] = $collection->findFirstByKeysAndKeyValues(
                ["Lön", "Sektor", "Kön", "År"],
                [ScbFormatter::$sektioner['0'], ScbFormatter::$kon['1+2'], $YEAR],
                'Medel'
            )->getValue();

            $lon['percentil10'] = $collection->findFirstByKeysAndKeyValues(
                ["Lön", "Sektor", "Kön", "År"],
                [ScbFormatter::$sektioner['0'], ScbFormatter::$kon['1+2'], $YEAR],
                'MedelPercentile10'
            )->getValue();

            $lon['percentil90'] = $collection->findFirstByKeysAndKeyValues(
                ["Lön", "Sektor", "Kön", "År"],
                [ScbFormatter::$sektioner['0'], ScbFormatter::$kon['1+2'], $YEAR],
                'MedelPercentile90'
            )->getValue();


            // Karta
            foreach ($regioner as $key => $values) {
                $region = $values['namn'];

                $anstalldaRegion = $collection->findFirstByKeysAndKeyValues(
                    ["Anställda", "Län", "Kön", "År"],
                    [$region, ScbFormatter::$kon['1+2'], $YEAR]
                )->getValue();

                $regioner[$key]['anstallda'] = $values['anstallda'] + $anstalldaRegion;
            }


            $r = [
                "anstallda" => $anstallda,
                "sektorer" => $sektorer,
                "lon" => $lon,
                "bristindex" => $this->getBristindex($yrkesgrupp),
                "ledigaJobb" => $this->jobSearchApi->getCount('yrkesgrupp', $yrkesgrupp->external_id),
                "regioner" => $regioner,
            ];

            $yrkesgrupp->update([
                'aggregated_statistics' => $r
            ]);

        }
    }

    public function getBristindex($yrkesgrupp)
    {
        return self::round($yrkesgrupp->bristindex()->ettAr()->maxArtal()->avg('bristindex'));
    }

    public function getBristindexForRegion($yrkesgrupp, $regionId)
    {
        return self::round($yrkesgrupp->bristindex()->ettAr()->maxArtal()->where('region_id', $regionId)->avg('bristindex'));
    }

    public static function round($value)
    {
        return (float) number_format(round($value, 2), 2, '.', '');
    }
}
