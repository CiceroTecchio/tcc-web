<?php

use App\Linha;
use App\Ponto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false, 'verify' => false]);

Route::group(['middleware' => 'auth','middleware' => 'check.active', 'prefix' => '/gerencial'], function () {
    Route::get('/home', 'HomeController@indexGerencial')->name('homeGerencial');

    Route::resource('/pontos', 'PontoController');

    Route::resource('/linhas', 'LinhaController');

    Route::resource('/veiculos', 'VeiculoController');

    Route::resource('/colaboradores', 'UserController');

    Route::resource('/roteiro/registro', 'RoteiroRegistroController');

    Route::delete('/usuarios/usuarios/{id}/admin', 'UserController@destroyAdmin')->name('destroyAdmin');

    Route::get('/linhas/mapa/create', function(){
        return view('linhas/mapa_cadastro_rota');
    })->name('linha_create_mapa');

    Route::get('/linhas/mapa/{id}/edit', 'LinhaController@editMAPA')->name('linha_edit_mapa');

    Route::put('/linhas/mapa/{id}/update', 'LinhaController@updateMAPA')->name('linha_update_mapa');

    Route::post('/create/linha', 'LinhaController@create')->name('create_linha');

    Route::get('/busca/linhas', 'LinhaController@indexAPI')->name('busca_linha');

    Route::get('/busca/rota/{id?}', 'LinhaController@rota')->name('busca_rota');

    Route::get('/busca/localizacao/{id?}', 'LocalizacaoRoteiroController@show');

});

Route::get('/', function(){
    if (Auth::check()) {
        return redirect('/gerencial/home');
    }else{
        $pontos = Ponto::select('id', 'latitude', 'longitude')->get();
        
        $linhas = Linha::join('roteiros_registro', 'cod_linha', 'linhas.id')
            ->where('linhas.fg_ativo', true)
            ->where('roteiros_registro.fg_ativo', true)
            ->select('linhas.id', 'nome', 'waypoints', 'origin', 'destination','roteiros_registro.fg_ativo')
            ->groupBy('roteiros_registro.cod_linha')
            ->get();

        return view('home', compact('pontos', 'linhas'));
    }
})->name('home');

Route::get('linhas', 'LinhaController@indexPublic')->name('linhasPublic');

Route::get('/busca/linhas', 'LinhaController@indexUser');

Route::get('/busca/rota/{id?}', 'LinhaController@rotaUser');

Route::get('/busca/localizacao/{id?}', 'LocalizacaoRoteiroController@showUser');

Route::get('/rotas/{id}', 'LinhaController@todasLinhas');