<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\YrkeseditorYrke as YrkeseditorYrkeResource;
use App\Services\YrkesgruppService;
use App\YrkeseditorYrke;
use Illuminate\Http\Request;

class YrkessokController extends Controller
{
    public function search(Request $request)
    {
        // If empty, return every yrkeseditorYrke
        if (empty($term = $request->input('q'))) {
            return YrkeseditorYrkeResource::collection(YrkeseditorYrke::all());
        }

        // Set a max length for the search term
        $term = substr($term, 0, 50);

        $yrkesgrupper = app(YrkesgruppService::class)->searchBySimilarity($term, 0.4);
        $yrkeseditorYrken = app(YrkeseditorYrke::class)->getByNameSimilarity($term, 0.4);

        foreach ($yrkesgrupper as $yrke) {
            $yrkeseditorYrkenBySsyk = app(YrkeseditorYrke::class)->whereSsyk($yrke->ssyk)->get();

            foreach ($yrkeseditorYrkenBySsyk as $yrkeBySsyk) {
                $yrkeBySsyk->similarity = $yrke->similarity;
                $yrkeBySsyk->fromTaxonomy = true;

                if ($yrkeseditorYrken->where('id', $yrkeBySsyk->id)->isEmpty()) {
                    $yrkeseditorYrken->push($yrkeBySsyk);
                }
            }
        }

        $yrkeseditorYrken = $yrkeseditorYrken->sortByDesc('similarity')->values();

        return YrkeseditorYrkeResource::collection($yrkeseditorYrken);
    }
}
