<?php

namespace App\Http\Controllers\Api\V1;

use App\Bristindex;
use App\Http\Controllers\Controller;
use App\Http\Resources\YrkesomradeCollection;
use App\Yrkesgrupp;
use App\Yrkesomrade;
use App\Http\Resources\Yrkesomrade as YrkesomradeResource;

class YrkesomradenController extends Controller
{
    public function index()
    {
        $yrkesomraden = Yrkesomrade::all();

        return new YrkesomradeCollection($yrkesomraden);
    }

    public function show($id)
    {
        $yrkesomrade = Yrkesomrade::where('id', $id)->with('yrkesgrupper', 'texts')->first();

        if (!$yrkesomrade) {
            abort(404);
        }

        return new YrkesomradeResource($yrkesomrade);
    }

}
