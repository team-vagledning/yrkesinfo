<?php

namespace App\Importers\Yrkesinfo\Yrkesgrupper;

use App\Importers\ImporterInterface;
use App\Yrkesgrupp;
use GuzzleHttp\Client;

class ApiImporter implements ImporterInterface
{
    const API_URL = 'https://arbetsformedlingen.se/rest/yrkesvagledning/rest/vagledning/';

    protected $client;

    protected $promises = [];
    protected $responses = [];

    private $ssykData = [];

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

        $oldYrkesgrupper = json_decode($results->getBody());

        foreach ($oldYrkesgrupper as $oldYrkesgrupp) {
            foreach ($oldYrkesgrupp->ssyk as $ssyk) {
                $this->ssykData[$ssyk->ssyk][] = $oldYrkesgrupp;
            }
        }

        $this->save();
    }

    public function save()
    {
        foreach ($this->ssykData as $ssyk => $data) {
            $yrkesgrupp = Yrkesgrupp::whereSsykOrAlternativeSsyk($ssyk)->first();
            if (!$yrkesgrupp) {
                continue;
            }
            
            $extras = $yrkesgrupp->extras;
            $extras['old_yrkesinfo'] = collect($data)->unique('id')->toArray();

            $yrkesgrupp->update(['extras' => $extras]);
        }
    }
}
