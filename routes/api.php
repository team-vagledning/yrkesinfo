<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::namespace('Api\V1')->prefix('v1')->group(function () {
    Route::get('yrkesomraden', 'YrkesomradenController@index');
    Route::get('yrkesomraden/{id}', 'YrkesomradenController@show');

    Route::get('yrkesomraden/{id}/yrkesgrupper/{ssyk}', 'YrkesgrupperController@showFromYrkesomrade');

    Route::get('yrkesgrupper', 'YrkesgrupperController@index');
    Route::get('yrkesgrupper/search', 'YrkesgrupperController@search');
    Route::get('yrkesgrupper/ssyk/{ssyk}', 'YrkesgrupperController@ssyk');
    Route::get('yrkesgrupper/{ssyk}', 'YrkesgrupperController@show');
});
