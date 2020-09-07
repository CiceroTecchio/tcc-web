<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/veiculos', 'VeiculoController@index');

    Route::get('/linhas', 'LinhaController@index');

    Route::post('/registros', 'RoteiroRegistroController@store');

    Route::delete('/registros/{id}', 'RoteiroRegistroController@destroy');

});

Route::post('/recuperar/senha', 'Auth\ForgotPasswordController@sendResetLinkEmail');

Route::post('auth/login', 'Auth\LoginController@LoginAPI');
