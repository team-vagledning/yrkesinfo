<?php

namespace App\Importers\Yrkesinfo\Yrkesgrupper;

use App\Importers\ImporterInterface;
use GuzzleHttp\Client;

class ApiImporter implements ImporterInterface
{
    const API_URL = 'https://arbetsformedlingen.se/rest/yrkesvagledning/rest/vagledning/';

    protected $client;

    protected $promises = [];
    protected $responses = [];

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::API_URL
        ]);
    }

    public function run()
    {
        // Fetch everything
        $results = $this->client->post('yrken/yrkessok', [
            'json' => [
                'yrkesomraden' => '',
            ]
        ]);

        // Flush the old cache
        cache()->tags('old-yrkesinfo')->flush();

        $yrkesgrupper = json_decode($results->getBody());

        foreach ($yrkesgrupper as $yrkesgrupp) {
            foreach ($yrkesgrupp->ssyk as $ssyk) {
                $this->cache($ssyk->ssyk, $yrkesgrupp);
            }
        }
    }

    public function cache($ssyk, $yrkesgrupp)
    {
        $cached = cache()->tags('old-yrkesinfo')->get($ssyk);

        if (!$cached) {
            $cached = [];
        }

        // Check if already cached
        foreach ($cached as $c) {
            if ($c->id == $yrkesgrupp->id) {
                return;
            }
        }

        array_push($cached, $yrkesgrupp);

        cache()->tags('old-yrkesinfo')->put($ssyk, $cached);
    }
}
