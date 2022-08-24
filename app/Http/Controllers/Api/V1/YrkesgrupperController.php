<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\YrkesgruppCollection;
use App\KeywordedYrkesgruppSearch;
use App\Services\YrkesgruppService;
use App\Yrkesbenamning;
use App\Yrkesgrupp;
use App\Http\Resources\Yrkesgrupp as YrkesgruppResource;
use App\Yrkesomrade;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class YrkesgrupperController extends Controller
{
    public function index(Request $request)
    {
        $yrkesgrupper = Yrkesgrupp::with('sunkoder')->get();

        return YrkesgruppResource::collection($yrkesgrupper);
    }

    public function showFromYrkesomrade($yrkesomradeId, $ssyk, Request $request)
    {
        // Load from yrkesområde
        $yrkesomrade = Yrkesomrade::where('external_id', $yrkesomradeId)->with('yrkesgrupper')->first();

        // Get the corresponding yrkesgrupp
        $yrkesgrupp = $yrkesomrade->yrkesgrupper->where('ssyk', $ssyk)->first();

        // Get all the yrkesgrupper, as siblings if specified
        if ($request->input('withYrkesgrupper')) {
            $yrkesgrupp->siblings = $yrkesomrade->yrkesgrupper()->get();
        }

        return new YrkesgruppResource($yrkesgrupp);
    }

    public function show($ssyk, Request $request)
    {
        $with = ['sunkoder', 'susanavetCourses'];

        if ($request->input('withYrkesprognosgrupper')) {
            $with[] = 'bristindexGroupings';
        }

        $yrkesgrupp = Yrkesgrupp::where('ssyk', $ssyk)->with($with)->first();

        if (!$yrkesgrupp) {
            abort(404);
        }

        // Fetch the first yrkesomrade, fine for now
        $yrkesomrade = $yrkesgrupp->yrkesomraden()->first();

        // Get all the yrkesgrupper, as siblings if specified
        if ($request->input('withYrkesgrupper')) {
            $yrkesgrupp->siblings = $yrkesomrade->yrkesgrupper()->get();
        }

        if ($request->input('withAllYrkesprognoser')) {
            $yrkesgrupp->yrkesprognoser = $yrkesgrupp->getYrkesprognoserWithAll();
        }

        return new YrkesgruppResource($yrkesgrupp);
    }

    public function ssyk($ssyk, Request $request)
    {
        $yrkesgrupper = Yrkesgrupp::where('ssyk', 'like', $ssyk . '%')->with('sunkoder', 'susanavetCourses')->get();

        if ($request->input('withYrkesgrupper')) {
            foreach ($yrkesgrupper as &$yrkesgrupp) {
                $yrkesomrade = $yrkesgrupp->yrkesomraden()->first();
                $yrkesgrupp->siblings = $yrkesomrade->yrkesgrupper()->get();
            }
        }


        return YrkesgruppResource::collection($yrkesgrupper);
    }

    public function search(Request $request)
    {
        if (empty($term = $request->input('q'))) {
            abort(400, "Search parameter is missing");
        }

        // Set a max length for the search term
        $term = substr($term, 0, 50);

        $cacheKey = "yrkesgrupper.search." . md5($term);
        if (Cache::has($cacheKey) && ! $request->has('clearCache')) {
            return Cache::get($cacheKey);
        }

        // Check for keyword search
        if (Str::startsWith($term, "k:")) {
            $keyworded = KeywordedYrkesgruppSearch::where('keyword', substr($term, 2))->with('yrkesgrupp')->get();
            $yrkesgrupper = $keyworded->pluck('yrkesgrupp')->flatten();
        } else {
            $yrkesgrupper = app(YrkesgruppService::class)->searchBySimilarity($term);
        }

        $resourceCollection = YrkesgruppResource::collection($yrkesgrupper);

        Cache::put($cacheKey, $resourceCollection, now()->addDays(30));

        return $resourceCollection;
    }
}
