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

Route::post('/v1/register', 'Api\V1\Auth\RegisterController@register');
Route::get('/v1/info', 'Api\V1\Auth\InfoController@info')->middleware('auth:api');