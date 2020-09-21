<?php

namespace App\Http\Controllers;

use App\Linha;
use Illuminate\Http\Request;
use App\LocalizacaoRoteiro;
use App\RoteiroRegistro;
use DB;
use Illuminate\Support\Facades\Auth;

class LocalizacaoRoteiroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->has('cod_roteiro_registro') && $request->has('latitude') && $request->has('longitude')) {
            $roteiro = RoteiroRegistro::find($request->cod_roteiro_registro);

            if ($roteiro == null) {
                return response()->json(['response' => 'Parâmetros Inválidos'], 400);
            } else if ($roteiro->cod_user != auth()->user()->id) {
                return response()->json(['response' => 'Parâmetros Inválidos'], 400);
            } else if ($roteiro->fg_ativo == false) {
                return response()->json(['response' => 'Parâmetros Inválidos'], 204);
            } else {

                try {
                    $localizacao = new LocalizacaoRoteiro();
                    $localizacao->fill($request->all());
                    $localizacao->save();

                    return response()->json(['response' => 'Localização Registrada'], 201);
                } catch (\Exception $e) {
                    return response()->json(['response' => 'Erro no Servidor'], 500);
                }
            }
        } else {
            return response()->json(['response' => 'Parâmetros Inválidos'], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id = null)
    {
        if ($id == null) {
            $linha = Linha::where('linhas.cod_empresa', Auth::user()->cod_empresa)
                ->leftJoin('roteiros_registro', function ($join) {
                    $join->on('cod_linha', 'linhas.id')
                        ->where('roteiros_registro.fg_ativo', true);
                })
                ->leftJoin('veiculos', 'cod_veiculo', 'veiculos.id')
                ->leftJoin('users', 'cod_user', 'users.id')
                ->leftJoin('localizacao_roteiro', function ($join) {
                    $join->on('cod_roteiro_registro', 'roteiros_registro.id')
                        ->on('localizacao_roteiro.id', '=', DB::raw("(SELECT max(id) from localizacao_roteiro)"));
                })
                ->where('linhas.fg_ativo', true)
                ->groupBy('cod_roteiro_registro')
                ->orderBy('localizacao_roteiro.id', 'desc')
                ->select('localizacao_roteiro.latitude', 'localizacao_roteiro.longitude', 'linhas.nome as linha', 'localizacao_roteiro.updated_at', 'users.name as colaborador', 'veiculos.identificador as veiculo')
                ->get();

            return response()->json(['response' => 'Acesso autorizado', 'localizacao' => $linha], 200);
        } else {
            $linha = Linha::where('linhas.cod_empresa', Auth::user()->cod_empresa)
                ->leftJoin('roteiros_registro', function ($join) {
                    $join->on('cod_linha', 'linhas.id')
                        ->where('roteiros_registro.fg_ativo', true);
                })
                ->leftJoin('veiculos', 'cod_veiculo', 'veiculos.id')
                ->leftJoin('users', 'cod_user', 'users.id')
                ->leftJoin('localizacao_roteiro', function ($join) {
                    $join->on('cod_roteiro_registro', 'roteiros_registro.id')
                        ->on('localizacao_roteiro.id', '=', DB::raw("(SELECT max(id) from localizacao_roteiro)"));
                })
                ->where('linhas.fg_ativo', true)
                ->where('linhas.id', $id)
                ->groupBy('cod_roteiro_registro')
                ->orderBy('localizacao_roteiro.id', 'desc')
                ->select('localizacao_roteiro.latitude', 'localizacao_roteiro.longitude', 'linhas.nome as linha', 'localizacao_roteiro.updated_at', 'users.name as colaborador', 'veiculos.identificador as veiculo')
                ->get();

            if ($linha == null) {
                return response()->json(['response' => 'Parâmetros Inválidos'], 400);
            }
            return response()->json(['response' => 'Acesso autorizado', 'localizacao' => $linha], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
