<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ValidarEstado
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

        if(!Auth::user()->isRole('superadmin')) {
            if (Auth::user()->empresa->estado != "A") {
                Auth::logout();
                return redirect('login')->with('estado', 'En este momento el estado de su empresa se encuentra inactivo, por tal motivo no podra iniciar sesión');
            } else if (Auth::user()->estado != 'A') {
                Auth::logout();
                return redirect('login')->with('estado', 'En este momento su estado se encuentra inactivo,por tal motivo no podra iniciar sesión, comuniquese con el administrador de su empresa');
            }
        }

        return $next($request);
    }
}
