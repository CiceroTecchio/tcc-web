<?php

namespace App\Http\Controllers;

use App\Linha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\RoteiroRegistro;
use App\Veiculo;

class RoteiroRegistroController extends Controller
{
   
    //verifica se existe um registro aberto para o usuário
    public function registroAberto()
    {
        $roteiro = RoteiroRegistro::where('cod_user', Auth::id())
            ->where('fg_ativo', true)
            ->get()
            ->first();

        if ($roteiro != null) {
            return response()->json(['response' => 'Linha em andamento', 'id' => $roteiro->id], 200);
        } else {
            return response()->json(['response' => 'Nenhuma linha em andamento']);
        }
    }
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
        //Valida se foi enviado o valor da linha e do veiculo
        if (!$request->has('cod_veiculo') || !$request->has('cod_linha')) {
            return response()->json(['response' => 'Parâmetros Inválidos'], 400);
        }

        //valida se a linha existe e pertence a empresa do usuário
        $linha = Linha::find($request->cod_linha);
        if ($linha == null) {
            return response()->json(['response' => 'Parâmetros Inválidos'], 400);
        } else if ($linha->cod_empresa != auth()->user()->cod_empresa) {
            return response()->json(['response' => 'Parâmetros Inválidos'], 400);
        }

        //valida se o veiculo existe e pertence a empresa do usuário
        $veiculo = Veiculo::find($request->cod_veiculo);
        if ($veiculo == null) {
            return response()->json(['response' => 'Parâmetros Inválidos'], 400);
        } else if ($veiculo->cod_empresa != auth()->user()->cod_empresa) {
            return response()->json(['response' => 'Parâmetros Inválidos'], 400);
        }

        try {
            RoteiroRegistro::where('cod_linha', $request->cod_linha)
                ->where('fg_ativo', true)
                ->update(['fg_ativo' => false]);

            $roteiro = new RoteiroRegistro();

            $roteiro->fill($request->all());
            $roteiro->fg_ativo = true;
            $roteiro->cod_user = Auth::id();
            $roteiro->save();

            return response()->json(['response' => 'Linha Iniciada', 'id' => $roteiro->id], 201);
        } catch (\Exception $e) {
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    //Desativa o registro de roteiro
    public function destroy($id)
    {
        $registro = RoteiroRegistro::select('roteiros_registro.cod_user', 'roteiros_registro.fg_ativo')->find($id);

        //verifica se o registro não existe
        if ($registro == null) {
            return response()->json(['response' => 'Parâmetros Inválidos'], 400);

            //verifica se o registro pertence ao usuário
        } else if ($registro->cod_user != auth()->user()->id) {
            return response()->json(['response' => 'Parâmetros Inválidos'], 400);

            //verifica se o registro já está inativo
        } else if ($registro->fg_ativo == false) {
            return response()->json(['response' => 'Linha já inativa'], 409);
        }

        //Inativa o registro
        try {
            RoteiroRegistro::where('id', $id)->update(['fg_ativo' => false]);

            return response()->json(['response' => 'Linha Finalizada'], 200);
        } catch (\Exception $e) {
            return response()->json(['response' => 'Parâmetros Inválidos'], 400);
        }
    }
}
