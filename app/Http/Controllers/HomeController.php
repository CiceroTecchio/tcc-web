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
        $pontos = Ponto::where('cod_empresa', Auth::user()->cod_empresa)
            ->select('id', 'latitude', 'longitude')
            ->get();

        $linhas = Linha::where('cod_empresa', Auth::user()->cod_empresa)
            ->join('roteiros_registro', 'cod_linha', 'linhas.id')
            ->where('linhas.fg_ativo', true)
            ->where('roteiros_registro.fg_ativo', true)
            ->select('linhas.id', 'nome', 'waypoints', 'origin', 'destination','roteiros_registro.fg_ativo')
            ->get();

        return view('homeGerencial', compact('pontos', 'linhas'));
    }
}
