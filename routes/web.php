<?php

use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false, 'verify' => false]);

Route::get('/', 'HomeController@index')->name('home');


Route::prefix('gerencial')->group(function () {
    Route::get('/home', 'HomeController@index')->name('homeGerencial');
});
