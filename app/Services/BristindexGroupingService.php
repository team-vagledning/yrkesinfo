<?php

namespace App\Services;

use App\BristindexGrouping;
use App\Yrkesbenamning;
use App\Yrkesgrupp;

class BristindexGroupingService
{
    public function searchBySimilarity($term)
    {
        $bristindexGroupings = BristindexGrouping::getByNameSimilarity($term, 0.4)->sortByDesc('similarity')->values()->all();
        $yrkesgrupper = Yrkesgrupp::getByNameSimilarity($term, 0.4)->sortByDesc('similarity')->values()->all();
        $yrkesbenamningar = Yrkesbenamning::getByNameSimilarity($term, 0.4)->sortByDesc('similarity')->values()->all();
        $sortedYrkesgrupper = [];
        $sortedBristindexGroupings = [];

        $updateIfBiggerSimilarity = function ($id, $similarity) use (&$sortedBristindexGroupings) {
            if (!isset($sortedBristindexGroupings[$id])) {
                $sortedBristindexGroupings[$id] = $similarity;
            } elseif ($sortedBristindexGroupings[$id] < $similarity) {
                $sortedBristindexGroupings[$id] = $similarity;
            }
        };

        foreach ($bristindexGroupings as $bristindexGrouping) {
            $updateIfBiggerSimilarity($bristindexGrouping->id, $bristindexGrouping->similarity);
        }

        foreach ($yrkesbenamningar as $yrkesbenamning) {
            foreach ($yrkesbenamning->yrkesgrupper as $yrkesgrupp) {
                foreach ($yrkesgrupp->bristindexGroupings->pluck('id') as $id) {
                    $updateIfBiggerSimilarity($id, $yrkesbenamning->similarity);
                }
            }
        }

        foreach ($yrkesgrupper as $yrkesgrupp) {
            foreach ($yrkesgrupp->bristindexGroupings->pluck('id') as $id) {
                $updateIfBiggerSimilarity($id, $yrkesgrupp->similarity);
            }
        }

        $sortedBristindexGroupings = collect($sortedBristindexGroupings)->map(function ($value, $key) {
            return [
               'id' => $key,
               'similarity' => $value
            ];
        })->sortByDesc('similarity')->unique('id')->values();


        return BristindexGrouping::with('yrkesgrupper')->whereIn('id', $sortedBristindexGroupings->pluck('id'))->get()
            ->each(function ($bristindexGrouping) use ($sortedBristindexGroupings) {
                $bristindexGrouping->similarity = (float) $sortedBristindexGroupings->keyBy('id')[$bristindexGrouping->id]['similarity'];
            })->sortByDesc('similarity')->values()->collect();
    }
}
