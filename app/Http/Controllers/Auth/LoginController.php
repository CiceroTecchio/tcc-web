<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Log;
use App\User;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    //Ao realizar login,
    function authenticated(Request $request, $user)
    {
        $log = new Log();
        $log->IP = $request->ip();
        $log->cod_user = $user->id;
        $log->save();
    }

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function credentials(Request $request)
    {
        return ['email' => $request->{$this->username()}, 'password' => $request->password, 'fg_ativo' => 1, 'fg_admin' => 1];
    }

    //Realiza o login para o APP
    function LoginAPI(Request $request)
    {

        //Verifica a quantidade de tentativas de acesso
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return response()->json(['response' => 'Muitas tentativas, aguarda para tentar novamente.'], 429);
        }

        //Se os dados estiverem corretos, reseta as quantidades de tentativa de login e retorna as informações
        //Gera um novo token e registra o acesso nos logs
        if (Auth::attempt(['email' => $request->email, 'password' => $request->senha, 'fg_ativo' => 1])) {
            $token = Str::random(60);
            $user = User::find(Auth::id());
            $user->api_token = $token;
            $user->save();

            $log = new Log();
            $log->IP = $request->ip();
            $log->cod_user = $user->id;
            $log->save();

            $this->clearLoginAttempts($request);
            return response()->json(['response' => 'Acesso autorizado', 'user' => Auth::user()->name, 'api_token' => $token], 200);

            //Se os dados forem incorretos, aumenta a quantidade de tentativas
        } else {
            $this->incrementLoginAttempts($request);
            return response()->json(['response' => 'Acesso não autorizado'], 401);
        }
    }
}
