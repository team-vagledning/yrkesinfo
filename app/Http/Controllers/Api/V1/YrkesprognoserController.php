<?php

namespace App\Http\Controllers\Api\V1;

use App\BristindexGrouping;
use App\Http\Controllers\Controller;
use App\Http\Resources\BristindexGroupingCollection;
use App\Http\Resources\Yrkesgrupp as YrkesgruppResource;
use App\KeywordedYrkesgruppSearch;
use App\Services\BristindexGroupingService;
use App\Services\YrkesgruppService;
use App\Yrkesomrade;
use App\Http\Resources\BristindexGrouping as BristindexGroupingResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class YrkesprognoserController extends Controller
{
    public function showFromYrkesomrade($yrkesomradeId, Request $request)
    {
        $cacheKey = "yrkesprognoser.yrkesomraden" . $yrkesomradeId;
        if (Cache::has($cacheKey) && ! $request->has('clearCache')) {
            return json_decode(Cache::get($cacheKey));
        }

        $yrkesomrade = Yrkesomrade::where('id', $yrkesomradeId)->with('bristindexGroupings')->first();

        if (!$yrkesomrade) {
            abort(404);
        }

        $resource = BristindexGroupingResource::collection(
            $yrkesomrade->bristindexGroupings()->distinct()->with('yrkesgrupper')->get()
        );

        Cache::put($cacheKey, json_encode($resource), now()->addDays(30));

        return $resource;
    }

    public function show($id)
    {
        $bristindexGrouping = BristindexGrouping::where('id', $id)->with('yrkesgrupper')->first();

        if (!$bristindexGrouping) {
            abort(404);
        }

        return new BristindexGroupingResource($bristindexGrouping);
    }

    public function search(Request $request)
    {
        if (empty($term = $request->input('q'))) {
            abort(400, "Search parameter is missing");
        }

        // Set a max length for the search term
        $term = substr($term, 0, 50);

        $cacheKey = "yrkesprognoser.search." . md5($term);
        if (Cache::has($cacheKey) && ! $request->has('clearCache')) {
            return Cache::get($cacheKey);
        }

        // Check for keyword search
        $bristindexGrouping = app(BristindexGroupingService::class)->searchBySimilarity($term);

        $resourceCollection = BristindexGroupingResource::collection($bristindexGrouping);

        Cache::put($cacheKey, $resourceCollection, now()->addDays(30));

        return $resourceCollection;
    }
}
