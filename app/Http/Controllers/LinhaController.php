<?php

namespace App\Http\Controllers;

use App\Linha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LinhaController extends Controller
{

    public function indexUser()
    {
        $linhas = Linha::join('roteiros_registro', 'cod_linha', 'linhas.id')
            ->where('linhas.fg_ativo', true)
            ->where('roteiros_registro.fg_ativo', true)
            ->select('linhas.id', 'nome')
            ->get();
        return response()->json(['response' => 'Acesso autorizado', 'linhas' => $linhas], 200);
    }

    public function rotaUser($id = null)
    {
        if ($id == null) {
            $linha = Linha::join('roteiros_registro', 'cod_linha', 'linhas.id')
                ->where('linhas.fg_ativo', true)
                ->where('roteiros_registro.fg_ativo', true)
                ->select('linhas.id', 'nome', 'origin', 'destination', 'waypoints')
                ->get();

            return response()->json(['response' => 'Acesso autorizado', 'linha' => $linha], 200);
        } else {
            $linha[0] = Linha::join('roteiros_registro', 'cod_linha', 'linhas.id')
                ->where('linhas.fg_ativo', true)
                ->where('roteiros_registro.fg_ativo', true)
                ->select('linhas.id', 'nome', 'origin', 'destination', 'waypoints')
                ->find($id);

            if ($linha == null) {
                return response()->json(['response' => 'Parâmetros Inválidos'], 400);
            }
            return response()->json(['response' => 'Acesso autorizado', 'linha' => $linha], 200);
        }
    }

    public function indexAPI()
    {
        $linhas = Linha::where('cod_empresa', Auth::user()->cod_empresa)->where('fg_ativo', true)->select('id', 'nome')->get();
        return response()->json(['response' => 'Acesso autorizado', 'linhas' => $linhas], 200);
    }

    public function rota($id = null)
    {
        if ($id == null) {
            $linha = Linha::where('cod_empresa', Auth::user()->cod_empresa)->where('fg_ativo', true)->select('id', 'nome', 'origin', 'destination', 'waypoints')->get();

            return response()->json(['response' => 'Acesso autorizado', 'linha' => $linha], 200);
        } else {
            $linha[0] = Linha::where('cod_empresa', Auth::user()->cod_empresa)->where('fg_ativo', true)->select('id', 'nome', 'origin', 'destination', 'waypoints')->find($id);
            if ($linha == null) {
                return response()->json(['response' => 'Parâmetros Inválidos'], 400);
            }
            return response()->json(['response' => 'Acesso autorizado', 'linha' => $linha], 200);
        }
    }

    public function index()
    {
        $linhas = Linha::where('cod_empresa', Auth::user()->cod_empresa)->get();

        return view('linhas/linhas', compact('linhas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->has('waypoints') && $request->has('origin') && $request->has('destination')) {
            $mapa = [];
            $mapa['waypoints'] = $request->waypoints;
            $mapa['origin'] = $request->origin;
            $mapa['destination'] = $request->destination;
            return view('linhas/create_linhas', compact('mapa'));
        } else {
            return redirect('gerencial/linhas')->with('error', 'Falha ao cadastrar rota!');
        }
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
            $linha = new Linha();
            $linha->cod_empresa = Auth::user()->cod_empresa;
            $linha->waypoints = $request->waypoints;
            $linha->origin = $request->origin;
            $linha->destination = $request->destination;
            $linha->fill($request->all());

            if ($request->fg_ativo == 'on') {
                $linha->fg_ativo = true;
            }
            if ($request->fg_domingo == 'on') {
                $linha->fg_domingo = true;
            }
            if ($request->fg_segunda == 'on') {
                $linha->fg_segunda = true;
            }
            if ($request->fg_terca == 'on') {
                $linha->fg_terca = true;
            }
            if ($request->fg_quarta == 'on') {
                $linha->fg_quarta = true;
            }
            if ($request->fg_quinta == 'on') {
                $linha->fg_quinta = true;
            }
            if ($request->fg_sexta == 'on') {
                $linha->fg_sexta = true;
            }
            if ($request->fg_sabado == 'on') {
                $linha->fg_sabado = true;
            }

            $linha->save();

            return redirect('gerencial/linhas')->with('success', 'Linha Cadastrada com Sucesso!');
        } catch (\Exception $e) {
            return redirect('gerencial/linhas')->with('error', 'Falha ao cadastrar rota!');
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
        $linha = Linha::find($id);

        if ($linha == null) {
            return redirect('gerencial/linhas')->with('error', 'Linha inválida!');
        } else if ($linha->cod_empresa != Auth::user()->cod_empresa) {
            return redirect('gerencial/linhas')->with('error', 'Linha inválida!');
        } else {

            return view('linhas/edit_linhas', compact('linha'));
        }
    }

    public function editMAPA($id)
    {
        $linha = Linha::find($id);

        if ($linha == null) {
            return redirect('gerencial/linhas')->with('error', 'Linha inválida!');
        } else if ($linha->cod_empresa != Auth::user()->cod_empresa) {
            return redirect('gerencial/linhas')->with('error', 'Linha inválida!');
        } else {

            return view('linhas/mapa_edit_rota', compact('linha'));
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
        $linha = Linha::find($id);
        if ($linha == null) {
            return redirect('gerencial/linhas')->with('error', 'Linha inválida!');
        } else if ($linha->cod_empresa != Auth::user()->cod_empresa) {
            return redirect('gerencial/linhas')->with('error', 'Linha inválida!');
        } else {
            try {
                $linha->fill($request->all());

                if ($request->fg_ativo == 'on') {
                    $linha->fg_ativo = true;
                } else {
                    $linha->fg_ativo = false;
                }

                if ($request->fg_domingo == 'on') {
                    $linha->fg_domingo = true;
                } else {
                    $linha->fg_domingo = false;
                }

                if ($request->fg_segunda == 'on') {
                    $linha->fg_segunda = true;
                } else {
                    $linha->fg_segunda = false;
                }

                if ($request->fg_terca == 'on') {
                    $linha->fg_terca = true;
                } else {
                    $linha->fg_terca = false;
                }

                if ($request->fg_quarta == 'on') {
                    $linha->fg_quarta = true;
                } else {
                    $linha->fg_quarta = false;
                }

                if ($request->fg_quinta == 'on') {
                    $linha->fg_quinta = true;
                } else {
                    $linha->fg_quinta = false;
                }

                if ($request->fg_sexta == 'on') {
                    $linha->fg_sexta = true;
                } else {
                    $linha->fg_sexta = false;
                }

                if ($request->fg_sabado == 'on') {
                    $linha->fg_sabado = true;
                } else {
                    $linha->fg_sabado = false;
                }

                $linha->save();

                return redirect('gerencial/linhas')->with('success', 'Linha Alterada com Sucesso!');
            } catch (\Exception $e) {
                return redirect('gerencial/linhas')->with('error', 'Falha ao alterar rota!');
            }
        }
    }

    public function updateMAPA(Request $request, $id)
    {
        $linha = Linha::find($id);
        if ($linha == null) {
            return redirect('gerencial/linhas')->with('error', 'Linha inválida!');
        } else if ($linha->cod_empresa != Auth::user()->cod_empresa) {
            return redirect('gerencial/linhas')->with('error', 'Linha inválida!');
        } else {
            try {

                $linha->waypoints = $request->waypoints;
                $linha->origin = $request->origin;
                $linha->destination = $request->destination;
                $linha->save();

                return redirect('gerencial/linhas')->with('success', 'Linha Alterada com Sucesso!');
            } catch (\Exception $e) {
                return redirect('gerencial/linhas')->with('error', 'Falha ao cadastrar rota!');
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
        $linha = Linha::find($id);
        if ($linha == null) {
            return back()->with('error', 'Linha inválida!');
        } else if ($linha->cod_empresa != Auth::user()->cod_empresa) {
            return back()->with('error', 'Linha inválida!');
        } else {
            $linha->fg_ativo = !$linha->fg_ativo;
            $linha->save();
            return back()->with('success', 'Linha alterada com sucesso!');
        }
    }
}
