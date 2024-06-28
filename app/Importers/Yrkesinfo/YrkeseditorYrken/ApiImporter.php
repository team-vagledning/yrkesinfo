<?php

namespace App\Importers\Yrkesinfo\YrkeseditorYrken;

use App\Importers\ImporterInterface;
use App\YrkeseditorYrke;
use App\Yrkesgrupp;
use GuzzleHttp\Client;

class ApiImporter implements ImporterInterface
{
    const API_URL = 'https://arbetsformedlingen.se/rest/yrkesvagledning/rest/vagledning/';

    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::API_URL
        ]);
    }

    public function run()
    {
        // Get yrken already in db
        $inDB = YrkeseditorYrke::get();

        // Fetch everything
        $results = $this->client->get('yrken');

        $yrkeseditorYrken = json_decode($results->getBody());

        foreach ($yrkeseditorYrken as $yrke) {
            YrkeseditorYrke::updateOrCreate([
                'id' => $yrke->id,
            ], [
                'data' => $yrke
            ]);
        }

        foreach ($inDB as $dbYrke) {
            if (!in_array($dbYrke->id, array_column($yrkeseditorYrken, 'id'), true)) {
                echo "Removing yrke with id {$dbYrke->id}\n";
                $dbYrke->delete();
            }
        }
    }
}
