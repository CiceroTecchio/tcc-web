<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

use Closure;

class CheckActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }else if (Auth::user()->fg_admin == false || Auth::user()->fg_ativo == false) {

            Auth::logout();
            return redirect()->route('login')->with('error', 'Suas permissões foram removidas, entre em contato com o administrador.');
        }
        return $next($request);
    }
}
