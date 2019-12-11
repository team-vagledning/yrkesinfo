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

            $regioner = resolve(Region::class)->get()->map(function ($region) use ($yrkesgrupp) {
                return [
                    'id' => $region->external_id,
                    'namn' => $region->name,
                    'anstallda' => 0,
                    'ledigaJobb' => $this->getNumOfAdsFromPlatsbanken($yrkesgrupp->ssyk, $region->external_id),
                    'bristindex' => $this->getBristindexForRegion($yrkesgrupp, $region->id)
                ];
            })->toArray();

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
                "bristindex" => $this->getBristindex($yrkesgrupp),
                "ledigaJobb" => $this->getNumOfAdsFromPlatsbanken($yrkesgrupp->ssyk),
                "regioner" => $regioner,
            ];

            $yrkesgrupp->update([
                'aggregated_statistics' => $r
            ]);

        }
    }

    public function getNumOfAdsFromPlatsbanken($ssyk, $regionId = false)
    {
        $results = [];

        $baseUrl = "https://www.arbetsformedlingen.se/rest/pbapi/af/v1/matchning/matchandeRekryteringsbehov";
        $payload = [
            "matchningsprofil" => [
                "profilkriterier" => [
                    [
                        "varde" => "**",
                        "namn" => "**",
                        "typ" => "FRITEXT"
                    ],
                    [
                        "varde" => $ssyk,
                        "namn" => "",
                        "typ" => "YRKESGRUPP_ROLL"
                    ]
                ]
            ],
            "sorteringsordning" => "RELEVANS",
            "startrad" => 0,
            "maxAntal" => 1,
        ];

        if ($regionId) {
            // Prepend a zero to regionId, ex. must be 01 and not 1
            if ($regionId < 10) {
                $regionId = "0{$regionId}";
            }

            $payload["matchningsprofil"]["profilkriterier"][] = [
                "varde" => $regionId,
                "namn" => "",
                "typ" => "LAN"
            ];
        }



        try {
            $client = new Client(['headers' => ['Accept' => 'application/json', 'Accept-Language' => 'sv']]);
            $response = $client->post($baseUrl, ['json' => $payload]);

            $results = json_decode($response->getBody()->getContents());
        } catch (\Exception $e) {

            return 0;
        }

        return data_get($results, 'antalPlatser', 0);
    }

    public function getBristindex($yrkesgrupp)
    {
        return self::round($yrkesgrupp->bristindex()->ettAr()->avg('bristindex'));
    }

    public function getBristindexForRegion($yrkesgrupp, $regionId)
    {
        return self::round($yrkesgrupp->bristindex()->ettAr()->where('region_id', $regionId)->avg('bristindex'));
    }

    public static function round($value)
    {
        return number_format(round($value, 2), 2, '.', '');
    }
}
