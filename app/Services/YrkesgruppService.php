<?php

namespace App\Services;

use App\Http\Resources\BristindexGrouping;
use App\Yrkesbenamning;
use App\Yrkesgrupp;

class YrkesgruppService
{
    public function searchBySimilarity($term, $similarity = 0.3)
    {
        $yrkesgrupper = Yrkesgrupp::getByNameSimilarity($term, $similarity)->sortByDesc('similarity')->values()->all();
        $yrkesbenamningar = Yrkesbenamning::getByNameSimilarity($term, $similarity)->sortByDesc('similarity')->values()->all();
        $sortedYrkesgrupper = [];

        foreach ($yrkesbenamningar as $yrkesbenamning) {
            $yrkesbenamning->yrkesgrupper->each(function ($yrkesgrupp) use (&$sortedYrkesgrupper, $yrkesbenamning) {
                array_push($sortedYrkesgrupper, [
                    'id' => $yrkesgrupp->id,
                    'similarity' => $yrkesbenamning->similarity,
                ]);
            });
        }

        foreach ($yrkesgrupper as $yrkesgrupp) {
            array_push($sortedYrkesgrupper, [
                'id' => $yrkesgrupp->id,
                'similarity' => $yrkesgrupp->similarity
            ]);
        }

        $sortedYrkesgrupper = collect($sortedYrkesgrupper)->sortByDesc('similarity')->unique('id')->values();

        return Yrkesgrupp::with('yrkesbenamningar')->whereIn('id', $sortedYrkesgrupper->pluck('id'))->get()
            ->each(function ($yrkesgrupp) use ($sortedYrkesgrupper) {
                $yrkesgrupp->similarity = (float) $sortedYrkesgrupper->keyBy('id')[$yrkesgrupp->id]['similarity'];
            })->sortByDesc('similarity')->values()->collect();
    }
}
