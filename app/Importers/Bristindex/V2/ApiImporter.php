<?php

namespace App\Importers\Bristindex\V2;

use App\Bristindex;
use App\Importers\ImporterInterface;
use App\Region;
use App\Yrkesgrupp;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ApiImporter implements ImporterInterface
{
    private $API_URL;

    public function __construct()
    {
        if (env('APP_ENV') === "production") {
            $this->API_URL = 'https://ipf.arbetsformedlingen.se:443/api-yrkesprognos/v1/';
        } else {
            $this->API_URL = 'https://ipf-acc.arbetsformedlingen.se:443/api-yrkesprognos/v1/';
        }

        $this->client = new Client([
            'base_uri' => $this->API_URL,
            'headers' => [
                'AF-TrackingId' => 1,
                'AF-SystemId' => 1,
                'AF-Environment' => env('AF_ENV'),
            ],
            'verify' => false,
            'timeout' => 10
        ]);
    }

    public function run()
    {
        try {
            $res = $this->client->get('yrkesprognos', ['query' => [
                'client_id' => env('IPF_CLIENT_ID'),
                'client_secret' => env('IPF_CLIENT_SECRET')
            ]]);

            $data = json_decode($res->getBody());

            file_put_contents(storage_path("imports/bristindex/saved-" . env('AF_ENV') . ".json"), json_encode($data));

        } catch (\Exception $e) {
            // Fallback to using saved data

            print "Timeout: Reading from file.\n";
            $data = json_decode(file_get_contents(storage_path("imports/bristindex/saved-" . env('AF_ENV') . ".json")));
        }


        foreach ($data->prognoser as $row) {

            $region_id = null;
            if ($row->geografi != "Riket") {
                $region_id = Region::where('name', $row->geografi)->firstOrFail()->id;
            }

            try {
                $yrkesgrupp = Yrkesgrupp::where('external_id', $row->concept_id)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                print "Could not find Yrkesgrupp with concept_id: {$row->concept_id}\n";
                continue;
            }


            $omfang = $row->ar - date('Y');

            Bristindex::updateOrCreate(
                [
                    'yrkesgrupp_id' => $yrkesgrupp->id,
                    'region_id' => $region_id,
                    'omfang' => $omfang,
                    'artal' => $row->ar,
                ],
                [
                    'bristindex' => $row->bristvarde,
                    'meta' => [
                        'ingress' => $row->ingress,
                        'stycke1' => $row->stycke1,
                        'stycke2' => $row->stycke2,
                        'stycke3' => $row->stycke3,
                    ]
                ]
            );

            print "Inserting bristindex for: " . $yrkesgrupp->ssyk . "\n";
        }
    }
}
