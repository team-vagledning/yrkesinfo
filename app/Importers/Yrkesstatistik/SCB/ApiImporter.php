<?php

namespace App\Importers\Yrkesstatistik\SCB;

use App\Importers\ImporterInterface;
use App\Yrkesgrupp;
use App\YrkesstatistikSource;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

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

        // For every yrkesgrupp (SSYK) we'll try to fetch statistics from every source
        foreach ($yrkesgrupper as $yrkesgrupp) {
            foreach ($sources as $source) {

                $statistics = $this->fetchStatistics($yrkesgrupp, $source);

                if ($valid = self::validStatistics($statistics)) {
                    $yrkesgrupp->yrkesstatistik()->create([
                        'yrkesstatistik_source_id' => $source->id,
                        'statistics' => $statistics
                    ]);
                }

                self::log($yrkesgrupp, $source, $valid);
            }
        }
    }

    /**
     * Just a logger, will output to terminal and log to file
     *
     * @param $yrkesgrupp
     * @param $source
     * @param bool $successful
     */
    public static function log($yrkesgrupp, $source, $successful = true)
    {
        $successful = $successful ? "Fetched" : "No statistics";
        $logMessage = "{$successful} {$source->supplier}::{$source->name} for {$yrkesgrupp->ssyk} ({$yrkesgrupp->name})";

        Log::info($logMessage);
        echo $logMessage . PHP_EOL;
    }

    /**
     * @param $yrkesgrupp
     * @param $source
     * @return string
     * @throws \Exception
     */
    public function fetchStatistics($yrkesgrupp, $source)
    {
        // Get endpoint and payload from the meta, and insert the correct SSYK
        [$endpoint, $payload] = $this->transformPayload($yrkesgrupp->alternativeSsykOrOriginal(), $source);

        // Try to fetch statistics five times, with 10s sleep between retries
        $results = retry(5, function () use ($endpoint, $payload) {
            return $this->client->post($endpoint, ['json' => $payload]);
        }, 10000);

        // Get the response. We also have to remove any BOM characters before decoding
        $contents = self::removeBOM($results->getBody()->getContents());

        // Return a assoc decoded array. In this case we'll use the Guzzle decoder as it will
        // throw any json decoder errors
        return \GuzzleHttp\json_decode($contents, true);
    }

    /**
     * @param $ssyk
     * @param $source
     * @return array
     * @throws \Exception
     */
    public function transformPayload($ssyk, $source)
    {
        // Ssyk should be in an array
        if (is_array($ssyk) === false) {
            $ssyk = [$ssyk];
        }

        // Set payload from meta
        $payload = $source->meta;

        // Set the endpoint and remove it from the payload
        $endpoint = $payload['endpoint'];
        Arr::forget($payload, 'endpoint');

        // Find the key to where to insert the SSYK
        $key = self::getQueryKey($payload['query'], 'Yrke2012');

        if ($key === false) {
            throw new \Exception("The payload for source {$source->id} is invalid, missing place to insert");
        }

        // Set SSYK in the payload
        data_set($payload, $key, $ssyk);

        // Return endpoint and payload as separate parts
        return [$endpoint, $payload];
    }

    public static function getQueryKey($query, $keyValue)
    {
        $key = false;
        foreach ($query as $k => $v) {
            if ($v['code'] === $keyValue) {
                $key = "query.{$k}.selection.values";
            }
        }

        return $key;
    }

    /**
     * Statistics from SCB is only valid if there's a column named Yrke2012
     *
     * @param $statistics
     * @return bool
     */
    public static function validStatistics($statistics) {
        foreach ($statistics['columns'] as $v) {
            if (data_get($v, 'code') === 'Yrke2012') {
                return true;
            }
        }

        return false;
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