<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Yrkesomrade;

class YrkesomradenController extends Controller
{
    public function index()
    {
        $yrkesomraden = Yrkesomrade::all('name', 'external_id', 'description');
        return response()->json($yrkesomraden);
    }
}