<?php

namespace App\Http\Controllers\Api\V1;

use App\BristindexYrkesgrupp;
use App\Http\Controllers\Controller;
use App\Yrkesgrupp;
use App\Yrkesomrade;
use App\Http\Resources\Yrkesomrade as YrkesomradeResource;

class YrkesomradenController extends Controller
{
    public function index()
    {
        $yrkesomraden = Yrkesomrade::all('id', 'name', 'external_id', 'description');

        return response()->json($yrkesomraden);
    }

    public function show($externalId)
    {
        $yrkesomrade = Yrkesomrade::where('external_id', $externalId)->with('yrkesgrupper')->first();

        return new YrkesomradeResource($yrkesomrade);
    }

}
