<?php

namespace App\Importers\KeywordedSearches;

use App\Importers\ImporterInterface;
use App\KeywordedYrkesgruppSearch;

class Yrkesgrupp implements ImporterInterface
{
    private static function bySsyk($ssyk)
    {
        return \App\Yrkesgrupp::whereSsykOrAlternativeSsyk($ssyk)->firstOrFail();
    }

    private function data()
    {
        return [
            [
                'keyword' => 'barnmorskor',
                'yrkesgrupper' => [2222]
            ],
            [
                'keyword' => 'civilingenjörsyrken',
                'yrkesgrupper' => [2141, 2142, 2143, 2144, 2145, 2146, 2149]
            ],
            [
                'keyword' => 'förskollärare',
                'yrkesgrupper' => [2343]
            ],
            [
                'keyword' => 'grundutbildade sjuksköterskor',
                'yrkesgrupper' => [2221]
            ],
            [
                'keyword' => 'läkare',
                'yrkesgrupper' => [2212, 2213, 2219]
            ],
            [
                'keyword' => 'läraryrken',
                'yrkesgrupper' => [2343, 2341, 2330, 2320, 2319, 2351,]
            ],
            [
                'keyword' => 'mjukvaru- och systemutvecklare',
                'yrkesgrupper' => [2512]
            ],
            [
                'keyword' => 'systemanalytiker och it-arkitekter',
                'yrkesgrupper' => [2511]
            ],
            [
                'keyword' => 'tandläkare',
                'yrkesgrupper' => [2260]
            ],
            [
                'keyword' => 'buss- och spårvagnsförare',
                'yrkesgrupper' => [8331]
            ],
            [
                'keyword' => 'byggnads- och ventilationsplåtslagare',
                'yrkesgrupper' => [7213]
            ],
            [
                'keyword' => 'kockar och kallskänkor',
                'yrkesgrupper' => [5120]
            ],
            [
                'keyword' => 'medicinska sekreterare och vårdadministratörer',
                'yrkesgrupper' => [4117]
            ],
            [
                'keyword' => 'målare',
                'yrkesgrupper' => [7131]
            ],
            [
                'keyword' => 'personliga assistenter',
                'yrkesgrupper' => [5343]
            ],
            [
                'keyword' => 'svetsare och gasskärare',
                'yrkesgrupper' => [7212]
            ],
            [
                'keyword' => 'träarbetare och snickare',
                'yrkesgrupper' => [7111]
            ],
            [
                'keyword' => 'elektriker',
                'yrkesgrupper' => [7413, 7412, 7411]
            ],
            [
                'keyword' => 'undersköterskor',
                'yrkesgrupper' => [5321, 5323]
            ],
            [
                'keyword' => 'specialistsjuksköterskor',
                'yrkesgrupper' => [2227, 2235, 2228, 2231, 2225, 2239, 2232, 2226, 2223]
            ],
        ];
    }

    public function run()
    {
        // Clear old keyworded
        KeywordedYrkesgruppSearch::truncate();

        foreach ($this->data() as $keyword) {
            foreach ($keyword['yrkesgrupper'] as $yrkesgrupp) {
                KeywordedYrkesgruppSearch::create([
                    'keyword' => $keyword['keyword'],
                    'yrkesgrupp_id' => self::bySsyk($yrkesgrupp)->id
                ]);
            }
        }
    }
}
