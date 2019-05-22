<?php

namespace App\Importers\Yrkesstatistik\SCB;

use App\Importers\ImporterInterface;
use App\Yrkesgrupp;
use App\YrkesstatistikSource;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class ApiImporter implements ImporterInterface
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function run()
    {
        $sources = $this->getSources();
        $yrkesgrupper = $this->getYrkesgrupper();

        foreach ($yrkesgrupper as $yrkesgrupp) {
            foreach ($sources as $source) {
                $statistics = $this->fetchStatistics($yrkesgrupp, $source);

                if (self::validStatistics($statistics)) {
                    echo "Fetched: {$source->supplier}::{$source->name} for {$yrkesgrupp->ssyk} ({$yrkesgrupp->name})\n";

                    $yrkesgrupp->yrkesstatistik()->create([
                        'yrkesstatistik_source_id' => $source->id,
                        'statistics' => $statistics
                    ]);
                } else {
                    echo "No statistics: {$source->supplier}::{$source->name} for {$yrkesgrupp->ssyk} ({$yrkesgrupp->name})\n";
                }
            }
        }
    }

    /**
     * @param $yrkesgrupp
     * @param $source
     * @return string
     * @throws \Exception
     */
    public function fetchStatistics($yrkesgrupp, $source)
    {
        [$endpoint, $payload] = $this->transformPayload($yrkesgrupp->ssyk, $source->meta);

        $results = retry(5, function () use ($endpoint, $payload) {
            return $this->client->post($endpoint, ['json' => $payload]);
        }, 10000);

        $contents = $results->getBody()->getContents();

        $decoded = \GuzzleHttp\json_decode(self::removeBOM($contents), true);

        return $decoded;
    }

    /**
     * @param $ssyk
     * @param $meta
     * @return array
     */
    public function transformPayload($ssyk, $meta)
    {
        $endpoint = $meta['endpoint'];
        $payload = $meta;

        $key = false;
        foreach ($meta['query'] as $k => $v) {
            if ($v['code'] === 'Yrke2012') {
                $key = $k;
            }
        }

        Arr::set($payload, "query.{$key}.selection.values", [$ssyk]);
        Arr::forget($payload, 'endpoint');

        return [$endpoint, $payload];
    }

    /**
     * @param $statistics
     * @return bool
     */
    public static function validStatistics($statistics) {
        $valid = false;

        foreach ($statistics['columns'] as $v) {
            if ($v['code'] === 'Yrke2012') {
                $valid = true;
            }
        }

        return $valid;
    }

    /**
     * @return YrkesstatistikSource[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getSources()
    {
        return YrkesstatistikSource::all();
    }

    /**
     * @return Yrkesgrupp[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getYrkesgrupper()
    {
        return Yrkesgrupp::all();
    }

    /**
     * @param $data
     * @return string
     */
    public static function removeBOM($data) {
        if (0 === strpos(bin2hex($data), 'efbbbf')) {
            return substr($data, 3);
        }
        return $data;
    }
}