<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\YrkeseditorYrke as YrkeseditorYrkeResource;
use App\Services\YrkesgruppService;
use App\YrkeseditorYrke;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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

        $cacheKey = "yrkessok.search." . md5($term);
        if (Cache::tags(['yrkessok'])->has($cacheKey) && ! $request->has('clearCache')) {
            return Cache::tags(['yrkessok'])->get($cacheKey);
        }

        if (Str::startsWith($term, "f:")) {
            $yrkeseditorYrken = app(YrkeseditorYrke::class)->getByFormagor(substr($term, 2));
        } else {
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
        }

        $yrken = YrkeseditorYrkeResource::collection($yrkeseditorYrken);

        Cache::tags(['yrkessok'])->put($cacheKey, $yrken, now()->addDays(30));

        return $yrken;
    }
}
