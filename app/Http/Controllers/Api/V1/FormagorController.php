<?php

namespace App\Http\Controllers\Api\V1;

use App\Collections\Formagor;
use App\Http\Controllers\Controller;
use App\YrkeseditorYrke;
use Illuminate\Http\Request;

class FormagorController extends Controller
{
    public function index(Request $request)
    {
        return new Formagor();
    }
}
