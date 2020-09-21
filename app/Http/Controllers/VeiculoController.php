<?php

namespace App\Http\Controllers;

use App\MarcaVeiculo;
use App\Veiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VeiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAPI()
    {
        $veiculos = Veiculo::where('cod_empresa', Auth::user()->cod_empresa)->where('fg_ativo', true)->select('id', DB::raw('concat(identificador," - ", placa) as nome'))->get();
        return response()->json(['response' => 'Acesso autorizado', 'veiculos' => $veiculos], 200);
    }


    public function index()
    {
        $veiculos = Veiculo::where('cod_empresa', Auth::user()->cod_empresa)
            ->join('marcas_veiculo', 'cod_marca', 'marcas_veiculo.id')
            ->select('veiculos.id', 'identificador', 'placa', 'marcas_veiculo.descricao_marca as marca', 'fg_ativo')
            ->get();

        return view('veiculos/veiculos', compact('veiculos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $marcas = MarcaVeiculo::all();

        return view('veiculos/create_veiculos', compact('marcas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $veiculo = new Veiculo();
            $veiculo->cod_empresa = Auth::user()->cod_empresa;
            $veiculo->fill($request->all());

            if ($request->fg_ativo == 'on') {
                $veiculo->fg_ativo = true;
            }
            $veiculo->save();

            return redirect('gerencial/veiculos')->with('success', 'Veículo Cadastrado com Sucesso!');
        } catch (\Exception $e) {
            return redirect('gerencial/veiculos')->with('error', 'Falha ao cadastrar Veículo!');
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
        $veiculo = Veiculo::find($id);

        if ($veiculo == null) {
            return redirect('gerencial/veiculos')->with('error', 'Veículo inválido!');
        } else if ($veiculo->cod_empresa != Auth::user()->cod_empresa) {
            return redirect('gerencial/veiculos')->with('error', 'Veículo inválido!');
        } else {
            $marcas = MarcaVeiculo::all();
            return view('veiculos/edit_veiculos', compact('veiculo', 'marcas'));
        }
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
        $veiculo = Veiculo::find($id);
        if ($veiculo == null) {
            return redirect('gerencial/veiculos')->with('error', 'Veículo inválido!');
        } else if ($veiculo->cod_empresa != Auth::user()->cod_empresa) {
            return redirect('gerencial/veiculos')->with('error', 'Veículo inválido!');
        } else {
            try {
                $veiculo->fill($request->all());

                if ($request->fg_ativo == 'on') {
                    $veiculo->fg_ativo = true;
                } else {
                    $veiculo->fg_ativo = false;
                }

                $veiculo->save();

                return redirect('gerencial/veiculos')->with('success', 'Veículo Alterado com Sucesso!');
            } catch (\Exception $e) {
                return redirect('gerencial/veiculos')->with('error', 'Falha ao alterar veículo!');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $veiculo = Veiculo::find($id);
        if ($veiculo == null) {
            return back()->with('error', 'Veículo inválido!');
        } else if ($veiculo->cod_empresa != Auth::user()->cod_empresa) {
            return back()->with('error', 'Veículo inválido!');
        } else {
            $veiculo->fg_ativo = !$veiculo->fg_ativo;
            $veiculo->save();
            return back()->with('success', 'Veículo alterado com sucesso!');
        }
    }
}
