<?php

namespace App\Http\Controllers;

use App\Ponto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PontoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pontos = Ponto::where('cod_empresa', Auth::user()->cod_empresa)->select('id', 'latitude', 'longitude')->get();

        return view('pontos', compact('pontos'));
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
        $markers = json_decode($request->markers);
        Ponto::where('cod_empresa', Auth::user()->cod_empresa)->delete();
        foreach($markers as $marker){
            $ponto = new Ponto();
            $ponto->latitude = $marker->lat;
            $ponto->longitude = $marker->lng;
            $ponto->cod_empresa = Auth::user()->cod_empresa;
            $ponto->save();
        }
        return redirect('gerencial/pontos')->with('success', 'Pontos alterados com Sucesso!');
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
    public function destroy($id)
    {
        //
    }
}
