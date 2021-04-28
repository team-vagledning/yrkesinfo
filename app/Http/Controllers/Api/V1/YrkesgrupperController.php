<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\YrkesgruppCollection;
use App\Yrkesbenamning;
use App\Yrkesgrupp;
use App\Http\Resources\Yrkesgrupp as YrkesgruppResource;
use App\Yrkesomrade;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        $yrkesgrupp = Yrkesgrupp::where('ssyk', $ssyk)->with('sunkoder')->first();

        if (!$yrkesgrupp) {
            abort(404);
        }

        // Fetch the first yrkesomrade, fine for now
        $yrkesomrade = $yrkesgrupp->yrkesomraden()->first();

        // Get all the yrkesgrupper, as siblings if specified
        if ($request->input('withYrkesgrupper')) {
            $yrkesgrupp->siblings = $yrkesomrade->yrkesgrupper()->get();
        }

        return new YrkesgruppResource($yrkesgrupp);
    }

    public function search(Request $request)
    {
        if (empty($term = $request->input('q'))) {
            abort(400, "Search parameter is missing");
        }

        // Set a max length for the search term
        $term = substr($term, 0, 50);

        $cacheKey = "yrkesgrupper.search.$term";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $yrkesgrupper = Yrkesgrupp::getByNameSimilarity($term)->sortByDesc('similarity')->values()->all();
        $yrkesbenamningar = Yrkesbenamning::getByNameSimilarity($term)->sortByDesc('similarity')->values()->all();
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

        $yrkesgrupper = Yrkesgrupp::with('yrkesbenamningar')->whereIn('id', $sortedYrkesgrupper->pluck('id'))->get()
            ->each(function ($yrkesgrupp) use ($sortedYrkesgrupper) {
                $yrkesgrupp->similarity = (float) $sortedYrkesgrupper->keyBy('id')[$yrkesgrupp->id]['similarity'];
            })->sortByDesc('similarity')->values()->collect();

        $resourceCollection = YrkesgruppResource::collection($yrkesgrupper);

        Cache::put($cacheKey, $resourceCollection, now()->addDays(30));

        return $resourceCollection;
    }
}
