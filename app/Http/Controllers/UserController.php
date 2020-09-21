<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuarios = User::where('cod_empresa', Auth::user()->cod_empresa)->where('id', '!=', Auth::user()->id)->get();

        return view('users/users', compact('usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users/create_users');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'size:14'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validatedData->fails()) {
            return back()->with('incorrectFields',  $validatedData->errors()->all());
        } else {
            try {
                $user = new User();
                $user->cod_empresa = Auth::user()->cod_empresa;
                $user->password = Hash::make($request->password);
                if ($request->fg_ativo == 'on') {
                    $user->fg_ativo = true;
                }
                if ($request->fg_admin == 'on') {
                    $user->fg_admin = true;
                }
                $user->fill($request->all());
                $user->save();

                return redirect('gerencial/colaboradores')->with('success', 'Colaborador cadastrado com Sucesso!');
            } catch (\Exception $e) {
                return redirect('gerencial/colaboradores')->with('error', 'Falha ao cadastrar Colaborador!');
            }
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
        $usuario = User::find($id);

        if ($usuario == null) {
            return redirect('gerencial/colaboradores')->with('error', 'Colaborador inválido!');
        } else if ($usuario->cod_empresa != Auth::user()->cod_empresa) {
            return redirect('gerencial/colaboradores')->with('error', 'Colaborador inválido!');
        } else {
            return view('users/edit_users', compact('usuario'));
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
        $usuario = User::find($id);

        $validatedData = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'size:14'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
        ]);

        if ($validatedData->fails()) {
            return back()->with('incorrectFields',  $validatedData->errors()->all());
        }
        
        if ($usuario == null) {
            return redirect('gerencial/colaboradores')->with('error', 'Colaborador inválido!');
        } else if ($usuario->cod_empresa != Auth::user()->cod_empresa) {
            return redirect('gerencial/colaboradores')->with('error', 'Colaborador inválido!');
        } else {
            try {
                $usuario->fill($request->all());

                if ($request->fg_ativo == 'on') {
                    $usuario->fg_ativo = true;
                } else {
                    $usuario->fg_ativo = false;
                }

                if ($request->fg_admin == 'on') {
                    $usuario->fg_admin = true;
                } else {
                    $usuario->fg_admin = false;
                }

                if ($request->password != null) {
                    $usuario->password = Hash::make($request->password);
                }

                $usuario->save();

                return redirect('gerencial/colaboradores')->with('success', 'Colaborador Alterado com Sucesso!');
            } catch (\Exception $e) {
                return redirect('gerencial/colaboradores')->with('error', 'Falha ao alterar Colaborador!');
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
        $user = User::find($id);
        if ($user == null) {
            return back()->with('error', 'Usuário inválido!');
        } else if ($user->cod_empresa != Auth::user()->cod_empresa) {
            return back()->with('error', 'Usuário inválido!');
        } else {
            $user->fg_ativo = !$user->fg_ativo;
            $user->save();
            return back()->with('success', 'Usuário alterado com sucesso!');
        }
    }

    public function destroyAdmin($id)
    {
        $user = User::find($id);
        if ($user == null) {
            return back()->with('error', 'Usuário inválido!');
        } else if ($user->cod_empresa != Auth::user()->cod_empresa) {
            return back()->with('error', 'Usuário inválido!');
        } else {
            $user->fg_admin = !$user->fg_admin;
            $user->save();
            return back()->with('success', 'Usuário alterado com sucesso!');
        }
    }
}
