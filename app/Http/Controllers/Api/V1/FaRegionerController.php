<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\FaRegion;
use Illuminate\Http\Request;

class FaRegionerController extends Controller
{
    public function index(Request $request)
    {
        return FaRegion::all();
    }
}
