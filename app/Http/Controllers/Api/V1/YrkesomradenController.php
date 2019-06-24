<?php

namespace App\Http\Controllers\Api\V1;

use App\BristindexYrkesgrupp;
use App\Http\Controllers\Controller;
use App\Yrkesgrupp;
use App\Yrkesomrade;

class YrkesomradenController extends Controller
{
    public function index()
    {
        $yrkesomraden = Yrkesomrade::all('name', 'external_id', 'description');
        return response()->json($yrkesomraden);
    }

    public function show($externalId)
    {
        $yrkesomrade = Yrkesomrade::where('external_id', $externalId)->first();

        return response()->json($yrkesomrade);
    }
}