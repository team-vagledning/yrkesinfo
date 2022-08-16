<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\YrkesgruppCollection;
use App\KeywordedYrkesgruppSearch;
use App\Services\YrkesgruppService;
use App\Yrkesbenamning;
use App\Yrkesgrupp;
use App\Http\Resources\Yrkesbenamning as YrkesbenamningResource;
use App\Yrkesomrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class YrkesbenamningarController extends Controller
{
    public function index(Request $request)
    {
        $yrkesbenamningar = Yrkesbenamning::get();

        return YrkesbenamningResource::collection($yrkesbenamningar);
    }

    public function search(Request $request)
    {
        if (empty($term = $request->input('q'))) {
            abort(400, "Search parameter is missing");
        }

        // Should we include yrkesgrupper
        $with = [];
        if ($request->input('withYrkesgrupper', false)) {
            $with = ['yrkesgrupper'];
        }

        // Set a max length for the search term
        $term = substr($term, 0, 50);

        $cacheKey = "yrkesbenamningar.search." . md5($term . implode('', $with));

        if (Cache::has($cacheKey) && ! $request->has('clearCache')) {
            return Cache::get($cacheKey);
        }

        $yrkesbenamningar = Yrkesbenamning::getByNameSimilarity($term, 0.3, $with);

        $resourceCollection = YrkesbenamningResource::collection($yrkesbenamningar);

        //Cache::put($cacheKey, $resourceCollection, now()->addDays(30));

        return $resourceCollection;
    }
}
