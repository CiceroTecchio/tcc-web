<?php

use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false, 'verify' => false]);

Route::get('/', 'HomeController@index')->name('home');

Route::get('/teste',function () {
    $users = DB::table('users')
    ->select('id', "name")
    ->get();

    return ["users"=> $users];
});

Route::prefix('gerencial')->group(function () {
    Route::get('/home', 'HomeController@index')->name('homeGerencial');
});
