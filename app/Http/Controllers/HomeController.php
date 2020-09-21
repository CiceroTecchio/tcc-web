<?php

namespace App\Http\Controllers;

use App\Linha;
use App\Ponto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function indexGerencial()
    {
        $pontos = Ponto::where('cod_empresa', Auth::user()->cod_empresa)->select('id', 'latitude', 'longitude')->get();

        $linhas = Linha::where('cod_empresa', Auth::user()->cod_empresa)->where('fg_ativo', true)->select('id', 'nome', 'waypoints','origin', 'destination')->get();

        return view('homeGerencial', compact('pontos', 'linhas'));
    }
}
