<?php

namespace App\Importers\JobSearch\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class ApiImporter
{
    const API_URL = 'https://jobsearch.api.jobtechdev.se';

    protected $client;

    protected $promises = [];
    protected $responses = [];

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::API_URL,
            'headers' => [
                'api-key' => env('JOBTECH_API_KEY')
            ]
        ]);
    }

    private function params($type, $taxonomyId, $regionId = false)
    {
        $params = [
            'X-Fields' => 'total{value}',
        ];

        switch ($type) {
            case 'yrkesomrade':
                $params['occupation-field'] = $taxonomyId;
                break;
            case 'yrkesgrupp':
                $params['occupation-grouo'] = $taxonomyId;
                break;
            default:
                throw new \Exception("Not a valid JobSearch type");
        }

        if ($regionId) {
            $params['region'] = $regionId;
        }

        return $params;
    }

    public function addAsyncSearch($type, $taxonomyId, $regionId = false)
    {
        $this->promises[$taxonomyId . $regionId] = $this->client->getAsync('search', [
            'query' => $this->params($type, $taxonomyId, $regionId)
        ]);
    }



    public function unwrap()
    {
        $this->responses = Promise\unwrap($this->promises);
        $this->promises = [];
    }

    public function getCount($type, $taxonomyId, $regionId = false)
    {
        $results = $this->client->get('search', [
            'query' => $this->params($type, $taxonomyId, $regionId)
        ]);

        return data_get(json_decode($results->getBody()), 'total.value', 0);
    }

    public function getAsyncCount($taxonomyId, $regionId)
    {
        return data_get(json_decode($this->responses[$taxonomyId . $regionId]->getBody()), 'total.value', 0);
    }
}
