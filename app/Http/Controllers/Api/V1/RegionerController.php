<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Region;
use Illuminate\Http\Request;

class RegionerController extends Controller
{
    public function index(Request $request)
    {
        return Region::all();
    }
}
