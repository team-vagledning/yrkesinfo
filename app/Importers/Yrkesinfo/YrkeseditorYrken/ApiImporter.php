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
    }
}
