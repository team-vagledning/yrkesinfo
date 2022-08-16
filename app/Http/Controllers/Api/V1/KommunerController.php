<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Kommun as KommunResource;
use App\Kommun;
use Illuminate\Http\Request;

class KommunerController extends Controller
{
    public function index(Request $request)
    {
        $kommuner = Kommun::with('region', 'faRegion')->get();
        return KommunResource::collection($kommuner);
    }

    public function showFromKommunkod($kommunkod, Request $request)
    {
        $kommun = Kommun::where('external_id', $kommunkod)->with('region', 'faRegion')->first();
        return new KommunResource($kommun);
    }
}
