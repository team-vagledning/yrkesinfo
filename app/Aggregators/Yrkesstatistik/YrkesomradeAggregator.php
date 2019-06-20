<?php

namespace App\Aggregators\Yrkesstatistik;

use App\Regioner;
use App\Yrkesomrade;
use App\YrkesstatistikAggregated;
use Arr;
use GuzzleHttp\Client;
use Str;

class YrkesomradeAggregator extends BaseAggregator
{
    const YEAR = '2017';

    public function run()
    {
        $YEAR = self::YEAR;

        //
        // Hur lösa medelvärden och viktade medelvärden?
        //

        $yrkesomraden = Yrkesomrade::take(1)->get();

        foreach ($yrkesomraden as $yrkesomrade) {

            $antalAnstallda = 0;

            $lon = [
               'medel' => 0,
               'percentil10' => 0,
               'percentil90' => 0,
            ];

            $regioner = collect(resolve(Regioner::class)->all())->mapWithKeys(function ($region, $id) use ($yrkesomrade) {
                return [
                    $region => [
                        'id' => $id,
                        'namn' => $region,
                        'anstallda' => 0,
                        'konkurrens' => 0,
                        'ledigaJobb' => $this->getAntalAnstalldaIRegion($yrkesomrade->external_id, $id),
                    ]
                ];
            })->toArray();

            foreach ($yrkesomrade->yrkesgrupper as $yrkesgrupp) {
                $aggregated = $yrkesgrupp->yrkesstatistikAggregated()->first();

                //dd(self::findVardeKeys($aggregated->statistics));

                //$keys = self::findVardeKeys($aggregated->statistics);
                $anstallda = data_get($aggregated->statistics, "anstallda.total.{$YEAR}.alla.varde");
                $medellon = data_get($aggregated->statistics, "lon.sektor.samtliga.{$YEAR}.alla.medellon.varde");
                $percentil10 = data_get($aggregated->statistics, "lon.sektor.samtliga.{$YEAR}.alla.percentil10.varde");
                $percentil90 = data_get($aggregated->statistics, "lon.sektor.samtliga.{$YEAR}.alla.percentil90.varde");

                $lon['medel'] += $anstallda * $medellon;
                $lon['percentil10'] += $anstallda * $percentil10;
                $lon['percentil90'] += $anstallda * $percentil90;

                $antalAnstallda += $anstallda;

                // Karta
                foreach ($regioner as $region => $values) {
                    $regioner[$region]['anstallda'] = $values['anstallda'] + data_get($aggregated->statistics, "anstallda.regioner.{$region}.{$YEAR}.alla.varde");
                }

            }

            $lon['medel'] = (int) round($lon['medel'] / $antalAnstallda);
            $lon['percentil10'] = (int) round($lon['percentil10'] / $antalAnstallda);
            $lon['percentil90'] = (int) round($lon['percentil90'] / $antalAnstallda);

            $r = [
                "lon" => $lon,
                "regioner" => $regioner,
            ];

            dd($r);

            $yrkesomrade->update([
                'aggregated_statistics' => $r
            ]);
        }

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

    public function getAntalAnstalldaIRegion($yrkesomradeId, $regionId)
    {
        $results = [];

        try {
            $url = "https://api.arbetsformedlingen.se/af/v0/platsannonser/matchning?lanid={$regionId}&yrkesomradeid={$yrkesomradeId}";

            $client = new Client(['headers' => ['Accept' => 'application/json', 'Accept-Language' => 'sv']]);
            $response = $client->get($url);

            $results = json_decode($response->getBody()->getContents());
        } catch (\Exception $e) {
            return 0;
        }

        return data_get($results, 'matchningslista.antal_platsannonser');
    }
}
