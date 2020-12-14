<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Importers\JobSearch\Api\ApiImporter;
use App\Modules\Yrkesstatistik\Collection;
use App\Region;
use App\Yrkesomrade;
use App\YrkesstatistikAggregated;
use Arr;
use GuzzleHttp\Client;
use Str;

class YrkesomradeAggregator extends BaseAggregator
{
    const YEAR = '2017';

    private $jobSearchApi;

    public function __construct(ApiImporter $jobSearchApi)
    {
        $this->jobSearchApi = $jobSearchApi;
    }

    public function run()
    {
        $YEAR = self::YEAR;

        //
        // Hur lösa medelvärden och viktade medelvärden?
        //

        $yrkesomraden = Yrkesomrade::get();

        foreach ($yrkesomraden as $yrkesomrade) {

            $antalAnstallda = 0;

            $lon = [
               'medel' => 0,
               'percentil10' => 0,
               'percentil90' => 0,
            ];

            $anstalldaSektorer = [
                'Offentlig' => 0,
                'Privat' => 0,
            ];

            $utbildningsstege = [];

            $regioner = resolve(Region::class)->get()->map(function ($region) use ($yrkesomrade) {

                $this->jobSearchApi->addAsyncSearch('yrkesomrade', $yrkesomrade->external_id, $region->external_id);

                return [
                    'id' => $region->external_id,
                    'namn' => $region->name,
                    'anstallda' => 0,
                    'ledigaJobb' => 0,
                    'bristindex' => $yrkesomrade->getBristindexes($region->id)['ett_ar']['varde'],
                ];
            })->toArray();

            $this->jobSearchApi->unwrap();

            foreach ($regioner as $key => $values) {
                $regioner[$key]['ledigaJobb'] = $this->jobSearchApi->getAsyncCount($yrkesomrade->external_id, $values['id']);
            }

            foreach ($yrkesomrade->yrkesgrupper as $yrkesgrupp) {
                $aggregated = $yrkesgrupp->yrkesstatistikAggregated()->orderBy('created_at', 'desc')->first();

                $collection = (new Collection())->initializeFromArray($aggregated->statistics);

                $anstallda = $collection->findFirstByKeysAndKeyValues(
                    ["Anställda", "Län", "Kön", "År"],
                    [ScbFormatter::$regioner['00'], ScbFormatter::$kon['1+2'], $YEAR]
                )->getValue();

                $sektorer = $collection->findAllByKeysAndKeyValues(
                    ["Anställda", "Sektor", "Kön", "År"],
                    [["Offentlig", "Privat"], ScbFormatter::$kon['1+2'], $YEAR]
                );

                foreach ($sektorer as $sektor) {
                    $anstalldaSektorer[$sektor->getKeyValue('Sektor')] += $sektor->getValue();
                }

                $medellon = $collection->findFirstByKeysAndKeyValues(
                    ["Lön", "Sektor", "Kön", "År"],
                    [ScbFormatter::$sektioner['0'], ScbFormatter::$kon['1+2'], $YEAR],
                    'Medel'
                )->getValue();

                $percentil10 = $collection->findFirstByKeysAndKeyValues(
                    ["Lön", "Sektor", "Kön", "År"],
                    [ScbFormatter::$sektioner['0'], ScbFormatter::$kon['1+2'], $YEAR],
                    'MedelPercentile10'
                )->getValue();

                $percentil90 = $collection->findFirstByKeysAndKeyValues(
                    ["Lön", "Sektor", "Kön", "År"],
                    [ScbFormatter::$sektioner['0'], ScbFormatter::$kon['1+2'], $YEAR],
                    'MedelPercentile90'
                )->getValue();

                $medellonUtbildningsniva = $collection->findAllByKeys(
                    ["Lön", "Utbildningsnivå", "Viktat", "År"]
                );

                foreach ($medellonUtbildningsniva as $mU) {
                    $ar = $mU->getKeyValue('År');
                    $utbildningsniva = $mU->getKeyValue('Utbildningsnivå');

                    $anstalldaUtbildningsniva = $collection->findFirstByKeysAndKeyValues(
                        ["Anställda", "Utbildningsnivå", "Enkel", "År"],
                        [$utbildningsniva, "Ja", $ar]
                    );

                    if ($anstalldaUtbildningsniva) {
                        data_inc($utbildningsstege, "{$utbildningsniva}.{$ar}.anstallda", $anstalldaUtbildningsniva->getValue());
                        data_inc($utbildningsstege, "{$utbildningsniva}.{$ar}.lon", $anstalldaUtbildningsniva->getValue() * $mU->getValue());
                    }
                }

                $lon['medel'] += $anstallda * $medellon;
                $lon['percentil10'] += $anstallda * $percentil10;
                $lon['percentil90'] += $anstallda * $percentil90;

                $antalAnstallda += $anstallda;

                // Karta
                foreach ($regioner as $key => $values) {
                    $region = $values['namn'];

                    $anstalldaRegion = $collection->findFirstByKeysAndKeyValues(
                        ["Anställda", "Län", "Kön", "År"],
                        [$region, ScbFormatter::$kon['1+2'], $YEAR]
                    )->getValue();

                    $regioner[$key]['anstallda'] = $values['anstallda'] + $anstalldaRegion;
                }

            }

            $lon['medel'] = (int) round($lon['medel'] / $antalAnstallda);
            $lon['percentil10'] = (int) round($lon['percentil10'] / $antalAnstallda);
            $lon['percentil90'] = (int) round($lon['percentil90'] / $antalAnstallda);

            $utbildningsstege = $this->makeUtbildningsstegeWeighted($utbildningsstege);

            $r = [
                "anstallda" => $antalAnstallda,
                "sektorer" => $this->anstalldaSektorerToArray($anstalldaSektorer),
                "lon" => $lon,
                "bristindex" => $yrkesomrade->getBristindexes()['ett_ar']['varde'],
                "ledigaJobb" => $this->jobSearchApi->getCount('yrkesomrade', $yrkesomrade->external_id),
                "regioner" => $regioner,
                'utbildningsstege' => $utbildningsstege
            ];

            $yrkesomrade->update([
                'aggregated_statistics' => $r
            ]);

        }
    }

    public function makeUtbildningsstegeWeighted($utbildningsstege)
    {
        return collect($utbildningsstege)->map(function ($item, $utbildningsniva) {
            return [
                'name' => $utbildningsniva,
                'varden' => collect($item)->map(function ($values, $year) {
                    return [
                        'ar' => $year,
                        'anstallda' => $values['anstallda'],
                        'lon' => (int) round($values['lon'] / $values['anstallda'])
                    ];
                })->sortByDesc('ar')->values()->all()
            ];
        })->values()->all();
    }

    public function anstalldaSektorerToArray($anstalldaSektorer)
    {
        return collect($anstalldaSektorer)->map(function ($anstalla, $sektor) {
            return [
                'name' => $sektor,
                'anstallda' => $anstalla
            ];
        })->values()->all();
    }

    public static function findVardeKeys($input)
    {
        return collect(Arr::dot($input))->filter(function ($value, $key) {
            return $value === 'värde' && Str::endsWith($key, 'typ');
        })->keys()->map(function ($key) {
            // Remove .typ from the key
            return substr($key, 0, -4);
        });
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
        return (float) number_format(round($value, 2), 2, '.', '');
    }
}
