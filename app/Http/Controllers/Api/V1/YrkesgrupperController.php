<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Yrkesgrupp;
use App\Http\Resources\Yrkesgrupp as YrkesgruppResource;
use App\Yrkesomrade;
use Illuminate\Http\Request;

class YrkesgrupperController extends Controller
{
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
        $yrkesgrupp = Yrkesgrupp::where('ssyk', $ssyk)->first();

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

}
