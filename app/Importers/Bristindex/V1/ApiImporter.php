<?php

namespace App\Importers\Bristindex\V1;

use App\Bristindex;
use App\Importers\ImporterInterface;
use App\Region;
use App\Yrkesgrupp;
use GuzzleHttp\Client;

class ApiImporter implements ImporterInterface
{
    const API_URL = 'https://arbetsformedlingen.se/rest/yrkesprognoser/Yrkeskomponent/';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::API_URL,
            'headers' => [
                'api-key' => env('JOBTECH_API_KEY')
            ]
        ]);
    }

    public function run()
    {
        // Fresh import
        Bristindex::truncate();

        foreach (Yrkesgrupp::get() as $yrkesgrupp) {
            $res = $this->client->get($yrkesgrupp->ssyk);
            $data = json_decode($res->getBody());
            $toInsert = [];

            if ($data) {
                $toInsert[] = [
                    'region_id' => null,
                    'yrkesgrupp_id' => $yrkesgrupp->id,
                    'omfang' => 5,
                    'bristindex' => $data->bedomning5ar,
                ];

                foreach ($data->lankommuner as $lan) {
                    $region = Region::where('name', $lan->lansnamn)->first();

                    $toInsert[] = [
                        'region_id' => $region->id,
                        'yrkesgrupp_id' => $yrkesgrupp->id,
                        'omfang' => 1,
                        'bristindex' => $lan->bristindex,
                    ];

                }

                Bristindex::insert($toInsert);

                print "Inserting bristindex for: " . $yrkesgrupp->ssyk . "\n";
            } else {
                print "Missing bristindex for: " . $yrkesgrupp->ssyk . "\n";
            }
        }
    }
}
